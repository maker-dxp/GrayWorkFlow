# 灰风工作平台用户API

## 登录

Method : POST

URL :  /User/login

Request:

> Header:
>
> | key | value |
> | ---- | ---- |
> |  none | none |

Params:

    {
        UserName:'',
        Password:'',
    }
----------------------------------------
Response:

>Header:
> 
>|  Key   | Value  |
>|  ----  | ----  |
>| Access-Token: | {jwt-token} |

Body:

    {
        code:200,
        message:'登录成功！',
        data:{
            UserName:'FeiBam',
            Icon:'http://static.GrayWindTech.com/',
            Point:10,
            Permission:['admin'],
            lastLoginAt:'2021-01-01',
        }
    }

- - -

## 获取用户信息

Method : GET

URL : /User/MyInfo

Request:

>Header:
> 
>| key | value |
>| ---- | ---- |
>| Access-Token: | {jwt-token} |

Params: None

- - -

Response:

> Header:
> 
> | key | value |
> | ---- | ---- |
> |  none | none |

Body:

    {
        code:200,
        message:'ok',
        data:{
            UserName:'FeiBam'
        }
    }

- - -

## 修改密码

Method : POST

URL : /User/Account/ChangePWD

Request:

> Header:
> 
> | key | value |
> | ---- | ---- |
> |  Access-Token | {jwt-token} |

Params:

    {
        OriginPWD:'' //老密码,
        NewPWD:''  // 新密码
    }

