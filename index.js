const axios = require('axios');
const cookieParser = require('cookie-parser');
const express = require('express');
const fr = require('./fr.json');
const morgan = require('morgan');
const pt = require('./pt.json');

const app = express();
const port = 80;

app.disable('x-powered-by');
app.set('view cache', false);
app.set('view engine', 'pug');
app.use(cookieParser());
app.use(express.urlencoded({ extended: true }));
app.use(express.static('public', { index: false, lastModified: false, maxAge: '7d' }));
app.use(morgan(':method :url :status'));

app.use((req, res, next) => {
  res.locals.localeMonth = (date) => {
    date = new Date(date);
    let language = res.locals.language || 'en';
    let month = date.toLocaleDateString(language, { month: 'short' });
    month = `${month.charAt(0).toUpperCase()}${month.slice(1)}`;
    let year = date.toLocaleDateString(language, { year: 'numeric' });
    return `${month}. ${year}`;
  }
  res.locals.token = req.cookies.t || null;
  res.locals.__ = (key) => {
    switch (res.locals.language) {
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

app.use('/:language(en|fr|pt)', (req, res, next) => {
  res.locals.language = req.params.language;
  next();
});

app.get('/:language/new', (req, res) => {
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

app.get('/:language(en|fr|pt)/:id', (req, res) => {
  axios.get(`https://api.digitalleman.com/v2/cvs/${req.params.id}`, {
    headers: {
      'authorization': `Bearer ${res.locals.token}`
    },
    params: {
      locale: res.locals.language,
      populate: [
        'educations',
        'experiences',
        'image',
        'interests',
        'miscellaneous',
        'skills'
      ]
    }
  })
  .then((response) => {
    if (response.data.data.attributes.locale == res.locals.language) {
      res.render('app', {
        cv: response.data
      });
    } else {
      res.redirect(301, `https://cv.digitalleman.com/${response.data.data.attributes.locale}/${response.data.data.id}`);
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

app.get('/:language(en|fr|pt)/:id/:componentType(educations|experiences|interests|miscellaneous|skills)/:componentID', (req, res, next) => {
  axios.get(`https://api.digitalleman.com/v2/cvs/${req.params.id}`, {
    headers: {
      'authorization': `Bearer ${res.locals.token}`
    },
    params: {
      locale: res.locals.language,
      populate: [
        'educations',
        'experiences',
        'interests',
        'miscellaneous',
        'skills'
      ]
    }
  })
  .then((response) => {
    const components = response.data.data.attributes[req.params.componentType];
    const component = components.find(component => component.id == req.params.componentID);
    if (component) {
      res.render('component', {
        component: component,
        components: components,
        componentType: req.params.componentType,
        cvID: response.data.data.id
      });
    } else if (req.params.componentID == 'new') {
      res.render('component', {
        components: components,
        componentType: req.params.componentType,
        cvID: response.data.data.id
      });
    } else {
      next();
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

app.post('/:language(en|fr|pt)/:id/:componentType(educations|experiences|interests|miscellaneous|skills)', (req, res, next) => {
  let components = JSON.parse(req.body.list);
  let data = {};
  components.push({
    description: req.body.description,
    endDate: `${req.body.endDate}-01`,
    startDate: `${req.body.startDate}-01`,
    title: req.body.title
  });
  data[req.params.componentType] = components;
  axios.put(`https://api.digitalleman.com/v2/cvs/${req.params.id}`, {
    data: data
  },
  {
    headers: {
      'authorization': `Bearer ${res.locals.token}`
    }
  })
  .then((response) => {
    res.redirect(`https://cv.digitalleman.com/${response.data.data.attributes.locale}/${response.data.data.id}`);
  }).catch((error) => {
    res.status(error.response.status || 500);
    res.send();
  });
});

app.post('/:language(en|fr|pt)/:id/:componentType(educations|experiences|interests|miscellaneous|skills)/:componentID', (req, res, next) => {
  let components = JSON.parse(req.body.list);
  let data = {};
  const index = components.findIndex(component => component.id == req.params.componentID);
  if (req.body.delete) {
    components.splice(index, 1);
  } else {
    components[index] = {
      endDate: `${req.body.endDate}-01`,
      id: req.params.componentID,
      startDate: `${req.body.startDate}-01`,
      title: req.body.title
    }
    if (['educations', 'experiences', 'miscellaneous'].includes(req.params.componentType)) components[index].description = req.body.description;
    if (['interests', 'skills'].includes(req.params.componentType)) components[index].text = req.body.text;
  }
  data[req.params.componentType] = components;
  
  axios.put(`https://api.digitalleman.com/v2/cvs/${req.params.id}`, {
    data: data
  },
  {
    headers: {
      'authorization': `Bearer ${res.locals.token}`
    }
  })
  .then((response) => {
    res.redirect(`https://cv.digitalleman.com/${response.data.data.attributes.locale}/${response.data.data.id}`);
  }).catch((error) => {
    res.status(error.response.status || 500);
  });
});

app.get('/pdf/:id', (req, res) => {
  axios.get(`https://api.digitalleman.com/v2/cvs/${req.params.id}`, {
    headers: {
      'authorization': `Bearer ${res.locals.token}`
    },
    params: {
      populate: [
        'educations',
        'experiences',
        'image',
        'interests',
        'miscellaneous',
        'picture',
        'skills'
      ]
    }
  })
  .then((response) => {
    let cv = response.data;

    if (cv.data.attributes.birthdate) {
      let date = new Date(cv.data.attributes.birthdate);
      cv.data.attributes.birthdate = `${date.toLocaleDateString(undefined, { day: '2-digit' })}/${date.toLocaleDateString(undefined, { month: '2-digit' })}/${date.getFullYear()}`;
      let difference = Date.now() - date.getTime();
      let age = new Date(difference);
      cv.data.attributes.age = Math.abs(age.getUTCFullYear() - 1970);
    }

    //if (cv.data.attributes.website) cv.data.attributes.websiteHostname = new URL(cv.data.attributes.website).hostname;

    if (cv.data.attributes.template) {    
      res.locals.language = cv.data.attributes.locale;
      res.render(`pdf/${cv.data.attributes.template}`, {
        cv: cv
      });
    }
  })
  .catch((error) => {
    res.status(error.response.status || 500);
    res.send();
  });
});

app.use((req, res) => {
  res.status(404);
  res.send();
});

app.use((err, req, res, next) => {
  res.status(500);
  res.send();
});

app.listen(port);