# 数据库表说明

## options表

配置信息表，用于存放一些配置信息

|字段|类型|说明|
|---|---|---|
|key|VARCHAR(128)|配置信息的键|
|value|TEXT|配置信息的值|

## users表

用户表，存放用户相关信息

|字段|类型|说明|
|---|---|---|
|user_id|INT|自增主键|
|user_name|VARCHAR(64)|登录用户名 唯一值 默认为空串|
|user_password|VARCHAR(128)|登陆密码 需先进行hash 默认为空串|
|nick_name|VARCHAR(64)|昵称 默认为空串|
|avatar|VARCHAR(256)|头像文件位置|
|authority|INT|权限码 默认为无权限|
|qq|VARCHAR(16)|不用解释了|
|point|INT|积分 默认为0|
|create_time|TIMESTAMP|创建时间|
|last_time|TIMESTAMP|最后登录时间|

## videos表

视频表，存放于视频相关的文件信息

|字段|类型|说明|
|---|---|---|
|video_id|INT|自增主键|
|video_origin_file|VARCHAR(256)|原视频路径|
|video_final_file|VARCHAR(256)|最终视频路径|
|trans_title|VARCHAR(256)|标题翻译|
|trans_draft|VARCHAR(256)|翻译草稿文件|
|trans_proof|VARCHAR(256)|翻译校对稿文件|
|trans_final|VARCHAR(256)|翻译终稿文件|
|axis_draft|VARCHAR(256)|时轴草稿文件|
|axis_proof|VARCHAR(256)|时轴校对稿文件|
|axis_final|VARCHAR(256)|时轴终稿文件|
|cover_untranslated|VARCHAR(256)|原始封面|
|cover_translated|VARCHAR(256)|完成封面|

## projects表

项目表，存放与项目相关的信息

|字段|类型|说明|
|---|---|---|
|project_id|INT|自增主键|
|video_file|INT|关联的视频表id，不能为空|
|video_name|VARCHAR(256)|原视频名称 不能为空|
|video_describe|TEXT|视频描述，可以是原视频简介，也可以是翻译后的|
|create_user|INT|创建该项目的用户，默认为nobody|
|create_time|TIMESTAMP|创建项目时间|
|status|INT|项目当前进度，默认为PROJ_CREATE|

## tasks表

任务表，表示任务与用户、任务与项目之间的关系，以及表示用户与项目之间的关系

|字段|类型|说明|
|---|---|---|
|task_id|INT|自增主键|
|user_id|INT|用户id，表示当前任务由谁来完成，默认为nobody|
|proj_id|INT|项目id，不可为空|
|type|INT|任务类型，不可为空|
|status|INT|任务当前状态，默认为S_NEED|
|level|INT|任务当前优先级，默认为L_NORMAL|