#灰风工作普通 管理员用户API

## 创建用户

Method : POST

URL : /api/Admin/User/add

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
    "Password": "GrayWind",     // 非必须  为了保证组员隐私 可以由服务器创建随机密码  
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
            "UserName": (String),
            "RandomPassword":(String), //如果是由服务器创建的密码 请务必带上
        }
    }
```

- - -

## 修改用户权限


