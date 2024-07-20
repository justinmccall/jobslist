const linkedIn = require('linkedin-jobs-api');

const queryOptions = {
  keyword: 'Senior Product Manager',
  location: 'Dubai',
  dateSincePosted: '24hr',
  jobType: 'full time',
  remoteFilter: '',
  salary: '',
  experienceLevel: '',
  limit: '1000'
};

linkedIn.query(queryOptions).then(response => {
  // console.log(response); // An array of Job objects

  var fs = require('fs');
  fs.writeFile("test.json", JSON.stringify(response), function(err) {
    if (err) {
      console.log(err);
    }
  });

});