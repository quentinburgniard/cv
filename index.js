const axios = require('axios');
const express = require('express');
const cookieParser = require('cookie-parser');
const logger = require('morgan');
const fr = require('./fr.json');
const pt = require('./pt.json');

const app = express();
const port = 80;

app.disable('x-powered-by');
app.use(cookieParser());
app.use(express.static('public', { maxAge: '7d' }));
app.use(express.urlencoded({ extended: false }));
app.use(logger('dev'));
app.set('view engine', 'pug');
app.disable('view cache');

app.use((req, res, next) => {
  res.__ = (key) => { 
    switch (req.params.language) {
      case 'fr':
        return fr[key] || key;
      case 'pt':
        return pt[key] || key;
      default:
        return key;
    }
  }
  next();
});

app.use((req, res, next) => {
  req.token = req.cookies.t || '';
  next();
});

app.get('/pdf/:id', (req, res) => {
  axios.get(`https://api.digitalleman.com/v2/cvs/${req.params.id}`, {
    headers: {
      'authorization': `Bearer ${req.token}`
    },
    params: {
      populate: [
        'educations',
        'experiences',
        'interests',
        'miscellaneous',
        'picture',
        'skills'
      ]
    }
  })
  .then((api) => {
    let cv = api.data.data;
    if (cv.attributes.birthDate) {
      let date = new Date(cv.attributes.birthDate);
      let day = date.getDate();
      let month = date.getMonth();
      cv.attributes.birthDate = `${day > 9 ? day : '0' + day}/${month > 9 ? month : '0' + month}/${date.getFullYear()}`;
      cv.attributes.age = Math.abs(new Date().getFullYear() - date.getFullYear());
    }
    if (cv.attributes.website) cv.attributes.websiteHostname = new URL(cv.attributes.website).hostname;
    res.render('PDF', {
      __: res.__,
      cv: cv
    });
  })
  .catch((error) => {
    res.status(error.response.status || 500);
    res.send();
  });
});

app.get('/:language', (req, res) => {
  let data = {
    locale: req.params.language
  };

  axios.get('https://api.digitalleman.com/v2/users/me', {
    headers: {
      'authorization': `Bearer ${req.token}`
    }
  })
  .then((api) => {
    data.email = api.data.email;
    axios.post('https://api.digitalleman.com/v2/cvs', { data: data }, {
      headers: {
        'authorization': `Bearer ${req.token}`
      }
    })
    .then((api) => {
      let cv = api.data.data;
      res.redirect(`https://cv.digitalleman.com/${cv.attributes.locale}/${cv.id}`);
    })
    .catch((error) => {
      res.status(error.response.status || 500);
      res.send();
    });
  })
  .catch((error) => {
    if (error && [401, 403].includes(error.response.status)) {
      res.redirect(`https://id.digitalleman.com?l=${req.params.language}&r=cv.digitalleman.com%2F${req.params.language}`);
    } else {
      res.status(error.response.status || 500);
      res.send();
    }
  });
});

app.get('/:language/:id', (req, res) => {
  axios.get(`https://api.digitalleman.com/v2/cvs/${req.params.id}`, {
    headers: {
      'authorization': `Bearer ${req.token}`
    },
    params: {
      locale: req.params.language,
      populate: [
        'educations',
        'experiences',
        'interests',
        'miscellaneous',
        'picture',
        'skills'
      ]
    }
  })
  .then((api) => {
    let cv = api.data.data;
    if (cv.attributes.locale == req.params.language) {
      res.render('App', {
        __: res.__,
        cv: cv
      });
    } else {
      res.redirect(301, `https://cv.digitalleman.com/${cv.attributes.locale}/${cv.id}`);
    }
  })
  .catch((error) => {
    if (error && [401, 403].includes(error.response.status)) {
      res.redirect(`https://id.digitalleman.com?l=${req.params.language}&r=cv.digitalleman.com%2F${req.params.language}%2F${req.params.id}`);
    } else {
      res.status(error.response.status || 500);
      res.send();
    }
  });
});

app.use((req, res) => {
  res.status(404);
  res.send();
});

app.use((err, req, res, next) => {
  res.status(err.status || 500);
  res.send();
});

app.listen(port);