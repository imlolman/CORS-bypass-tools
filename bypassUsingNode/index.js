const express = require('express')
const request = require('request');
const app = express()
const path = require('path');
const port = 3000

app.get('/', (req, res) => {
  var url = req.query.url;
  if(url != undefined){
    request(url, function (error, response, body) {
      if (!error && response.statusCode == 200) {
        res.header("Access-Control-Allow-Origin", "*");
        res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
        res.send(body);
      }
    });
  }else{
    res.sendFile(path.join(__dirname+'/usage.txt'))
  }
})

app.listen(port, () => console.log(`Example app listening on port ${port}!`))