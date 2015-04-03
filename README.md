#微信公众平台开发PHP版类库
微信公众平台开发是越来越火，虽说微信有官方文档指导开发者进行开发， 为了方便大家，特此封装了一些常用方法。
##微信官方文档
[点击打开官网](http://mp.weixin.qq.com/wiki/home/index.html "微信公众平台官网")
##版权声明
本程序基于MIT协议
##作者主页
[每天进步一点点](http://www.ddhigh.com "每天进步一点点")
##功能
1. 基础支持
    + getAccessTokenFromRemote 从微信服务器读取AccessToken
    + getWechatServerAddress 获取微信服务器IP地址
2. 基本交互
    + request 读取用户向公众平台发送的数据
    + response 公众平台向用户发送被动响应
3. 客服接口
    + addCustomer 添加客服账号
    + updateCustomer 更新客服账号
    + deleteCustomer 删除客服账号
    + setCustomerAvatar 设置客服头像，支持远程图片
    + getCustomers 获取客服列表
    + sendCustomerMessage 发送客服消息
4. 高级群发接口
    + uploadNews 上传图文素材
    + sendMassToGroup 根据分组群发
    + uploadVideo 上传视频（高级群发接口中的所有视频类消息需请求本接口上传视频换取media_id）
    + sendMassToOpenids 根据OPENID列表群发
    + deleteMass 删除群发消息
    + previewMass 预览群发消息
    + getMassStatus 获取群发消息状态
5. 模板消息接口
    + setTemplateIndustry 设置所属行业
    + getTemplateId 获得模板ID
    + sendTemplate 发送模板消息
