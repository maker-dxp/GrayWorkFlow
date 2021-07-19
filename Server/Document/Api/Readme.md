# 灰风字幕组API文档

用户API的注意事项:

[用户API](./User/Readme.md)

除去登录API 之外的所有API 其余全部API 请务必携带

Access-Token 头 ，否则会被400

一些通用的用户错误码

Code:
>> | code | message |
>> | ---- | ---- |
>> | 401 | 你没有登录 |
>> | 4010 | 没有这个Token |
>> | 4011 | Token过期了 |