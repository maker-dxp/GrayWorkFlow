const express = require('express')
const app = express()


app.use(async (req,res) => {
    res.status(200)
    res.send('hello world')
})

app.listen(8080)