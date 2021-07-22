const Koa = require('koa')
const KoaCors = require('@koa/cors')
const KoaBody = require('koa-bodyparser')

const { Router } = require('./routes/index')



const App = new Koa()
console.log(Router.Router)

App.use(async (ctx,next)=>{
    console.log(ctx)
    await next()
})
App.use(Router.Router.routes())

App.listen(8080)