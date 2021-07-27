const Koa = require('koa')
const KoaCors = require('@koa/cors')
const KoaBody = require('koa-bodyparser')

const { Router } = require('./routes/main')


const App = new Koa()

App.use(KoaBody())

App.use(KoaCors({
    origin:'*'
}))

App.use(async (ctx,next)=>{
    await next()
})

App.use(Router.routes())

App.listen(8080)