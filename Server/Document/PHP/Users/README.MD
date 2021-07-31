# Widget_User类说明

## 常量

**常量说明见[Code](../Code/README.MD)。**

---

## 静态方法

### 用户认证

通过密码或token来认证一个用户是否合法。合法用户将会返回该用户对象。

> 函数原型 `public static function verify(mixed $identity): Widget_User`

参数：

- `$identity`：

    若变量为string，则尝试通过将该string解析为token，创建一个用户对象。如果传入一个数组，
  则尝试在数组中查找`UserName`和`Password`，如果找不到，则抛出`Widget_User_Exception`异常。
  
  如果验证通过，会返回Widget_User对象，如果失败，抛出`Widget_User_Exception`异常。

### 工厂方法

通过uid来创建一个用户对象。

> 函数原型 `public static function factory(int $uid): Widget_User`

参数：

- `$uid`:
  
  想要创建的用户的uid。

---

## 成员方法

### 新建用户

**注：** 仅有管理员可以创建用户，因此必须确保调用该函数的对象拥有`C_ADMIN`权限。

> 函数原型 `public function create(array $data)`

参数：

- `$data`:

  该参数必须为`array`类型。参数中可包括以下内容。标*的为必选项。
```php
    'UserName'      =>      [登录用户名]*,
    'Password'      =>      [用户密码]，
    'NickName'      =>      [用户昵称],
    'Icon'          =>      [用户头像],
    'QQ'            =>      [用户QQ号]
```

异常：

- 如果调用函数的对象没有`C_ADMIN`权限，抛出`Widget_User_Exception`异常，状态码为`PERMISSION_DENIED`。
- 如果数组中缺少`UserName`，抛出`Widget_User_Exception`异常，状态码为`EMPTY_BODY_FIELD`。
- 如果发生数据库错误，抛出`Widget_User_Exception`异常，状态码为`HTTP_SERVER_ERROR`。

### 发送Token至响应头

将调用对象的token写入响应头。

> 函数原型 `public function sendToken()`


### 积分调整
增加积分
> 函数原型 `public function addPoint(int $point)`

消费积分
> 函数原型 `public function rewardPoint(int $point)`

### 保存修改
如果进行过数据的更改，最后可以使用这个函数保存。因为数据的修改是被写入到缓存的。当然，如果不保存，在该对象被析构时也会自动保存，但是无法处理异常。

> 函数原型 `public function save()`

### 验证管理员

> 函数原型 `public function isAdmin()`