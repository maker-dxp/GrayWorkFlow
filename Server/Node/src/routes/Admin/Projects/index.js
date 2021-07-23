const KoaRouter = require('@koa/router')
const Router = new KoaRouter()


Router.get('/add',async (ctx,next)=>{
    ctx.body = 'add ok'
})

module.exports = Router