const axios = require('axios');
const createError = require('http-errors');
const express = require('express');
const cookieParser = require('cookie-parser');
const logger = require('morgan');
const fr = require('./fr.json');

const app = express();
const port = 8080;

app.disable('x-powered-by');
app.use(cookieParser());
app.use(express.static('public', { maxAge: '7d' }));
app.use(express.urlencoded({ extended: false }));
app.use(logger('dev'));
app.set('view engine', 'pug');
app.disable('view cache');

app.use((req, res, next) => {
  res.__ = (key) => { 
    switch (res.language) {
      case 'fr':
        return fr[key] || key;
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

app.get('/:id/:language', (req, res, next) => {
  res.language = req.params.language;
  axios.get('https://api.digitalleman.com/v2/cvs/' + req.params.id, {
    headers: {
      'authorization': `Bearer ${req.token}`
    },
    params: {
      populate: [
        'educations',
        'experiences',
        'interests',
        'miscellaneous',
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
    res.render('App', {
      __: res.__,
      cv: api.data.data
    });
  })
  .catch(function (error) {
    //console.log(error);
    res.sendStatus(error.response.status);
  });
});

app.use(function(req, res, next) {
  res.sendStatus(404);
});

app.use(function(err, req, res, next) {
  res.sendStatus(err.status || 500);
});

app.listen(port);