const KoaRouter = require('@koa/router')
const Router = new KoaRouter()


Router.get('/test',async (ctx,next)=>{
    ctx.body = 'ok'
})


Router.post('/login',async (ctx,next)=>{
    console.log(ctx.request.body)
    ctx.set('Access-Token','abcdefg')
    ctx.status = 200
    return ctx.body = {
        code:200,
        message:'成功',
        data:{}
    }
})

Router.get('/info',async (ctx,next)=>{
    console.log(ctx.request.body)
    ctx.set('Access-Token','abcdefg')
    ctx.status = 200
    return ctx.body = {
        code:200,
        message:'成功',
        data:{
            UserName:'FeiBam',
            Icon:'https://lh3.googleusercontent.com/ogw/ADea4I4blpmXSoI9hYn_6E3d8V46UlF3qJbKOsoZ6IuZ=s83-c-mo',
            Point:10,
            Permission:['admin'],
            lastLoginAt:'2021-01-01 19:23',
        }
    }
})
module.exports = Router