# 灰风工作平台用户API

## 登录

Method : POST

URL :  /api/User/login

Request:

> Header:
>
> | key | value |
> | ---- | ---- |
> |  none | none |

Params:

    {
        "UserName": (String),
        "Password": (String)
    }
----------------------------------------
Response:

>Header:
> 
>|  Key   | Value  |
>|  ----  | ----  |
>| Access-Token: | [jwt-token] |

Body:

    {
        code:200,
        message:'成功',
        data:{}
    }

- - -

## 获取用户信息

Method : GET

URL : /api/User/info

Request:

>Header:
> 
>| key | value |
>| ---- | ---- |
>| Access-Token: | [jwt-token] |

Body:none


- - -

Response:

> Header:
> 
> | key | value |
> | ---- | ---- |
> |  none | none |

Body:

```
    {
        "code": 200,
        "message": "成功",
         "data": {
            "UserName": (String),
            "Icon": (String),
            "Point": (Number),
            "Permission": [(String)], // 传递用户有的职位
            "lastLoginAt": (YYYY-MM-DD   HH:MM)
        }
    }
```

- - -

## 修改密码

Method : POST

URL : /api/User/pwd

Request:

> Header:
> 
> | key | value |
> | ---- | ---- |
> |  Access-Token | [jwt-token] |

Body:

```
    {
        "OriginPWD":(String)   //老密码 必须
        "NewPWD":(String)     //新密码 必须
    }
```

Response:

Head:None

Body:
```

    {
        "code": 200,
        "message": "修改密码成功",
        "data":{}
    }

```

- - -

## 修改昵称

Method : POST

URL : /api/User/name

Request:

> Header:
>
> | key | value |
> | ---- | ---- |
> |  Access-Token | [jwt-token] |

Body:

```
    {
        "UserName":(String)
    }
```

- - -

Response:

Head:none

Body:

```

    {
        "code": 200,
        "message": "修改用户名成功！",
        "data":{}
    }

```