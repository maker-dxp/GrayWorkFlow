# 灰风工作平台用户API

## 登录

Method : POST

URL :  /api/user/login

Request:

> Header:
>
> | key | value |
> | ---- | ---- |
> |  none | none |

Params:

    {
        "UserName": "",
        "Password": ""
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

URL : /api/user/info

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
            "UserName": "test",
            "DisplayName": "test2",
            "Icon": "default.png",
            "Point": "0",
            "Permission": null,
            "LastLoginAt": "2021-07-23 23:11:16"
        }
    }
```

- - -

## 创建用户

Method : PUT

URL : /api/user/info

Request:

>Header:
>
>| key | value |
>| ---- | ---- |
>| Access-Token: | [jwt-token] |

Body:

```
{
    "UserName": "GrayWind",     //必须
    "Password": "GrayWind",     //必须
    "DisplayName": "GrayWind",
    "Icon": "/path/icon/xxx.png"
}
```

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
            "UserName": "test",
            "DisplayName": "test2",
            "Icon": "default.png",
            "Point": "0",
            "Permission": null,
            "LastLoginAt": "2021-07-23 23:11:16"
        }
    }
```

- - -

## 修改密码

Method : POST

URL : /api/user/pwd

Request:

> Header:
> 
> | key | value |
> | ---- | ---- |
> |  Access-Token | [jwt-token] |

Body:

```
    {
        "uid": 0        //只有管理员可以设置其他人的密码
        "OriginPWD":""    //老密码
        "NewPWD":""       //新密码       如果不存在新密码则会随机设置一个密码
    }
```

Response:

```
    {
        "OriginPWD":""    //老密码
        "NewPWD":""       //新密码       
    }
```

- - -

## 修改昵称

Method : POST

URL : /api/user/name

Request:

> Header:
>
> | key | value |
> | ---- | ---- |
> |  Access-Token | [jwt-token] |

Body:

```
    {
        "DisplayName": "名字"
    }
    
    //如果是管理员，则可以修改其他人的名称
    {
        "uid": 1,       //要修改的用户
        "DisplayName": "名字"
    }
```