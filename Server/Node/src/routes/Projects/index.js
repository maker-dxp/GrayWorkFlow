const KoaRouter = require('@koa/router')
const Router = new KoaRouter()


Router.get('/test',async (ctx,next)=>{
    ctx.body = 'ok'
})

module.exports = Router