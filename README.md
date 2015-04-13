#微信公众平台开发PHP版类库
微信公众平台开发是越来越火，虽说微信有官方文档指导开发者进行开发， 为了方便大家，特此封装了一些常用方法。    
注：本程序自带文件缓存类，以缓存诸如access_token,jsapi_ticket等参数
##微信官方文档
[点击打开官网](http://mp.weixin.qq.com/wiki/home/index.html "微信公众平台官网")
##版权声明
本程序基于MIT协议
## 使用教程
1. 引入autoload.php
2. 实例化Wechat类即可
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
6. 素材管理接口
    + uploadTempMedia 上传临时素材
    + getTempMedia 获取临时素材
    + addMaterialNews 上传永久图文素材
    + uploadMaterial 上传永久其他素材
    + uploadMaterialVideo 上传永久视频素材
    + getMaterial 获取永久素材
    + deleteMaterial 删除永久素材
    + updateMaterialNews 编辑永久图文素材
    + getMaterialCount 获取永久素材总数
    + getMaterialList 获取永久素材列表
7. 用户管理
    + createGroup 创建用户分组
    + getGroups 获取所有用户分组
    + getUserGroup 获取指定用户所在分组
    + updateGroup 更新用户分组信息
    + moveUserToGroup 移动用户分组
    + moveUsersToGroup 批量移动用户分组
    + deleteGroup 删除用户分组
    + setUserReMark 设置用户备注
    + getUserInfo 获取关注者信息
    + getSubscribeOpenids 获取关注者列表
    + getOauth2Url 获取授权链接
    + getOauth2AccessToken 根据授权CODE换取access_token
    + refreshOauth2AccessToken 刷新授权access_token
    + getOauth2UserInfo 获取授权用户信息
    + isOauth2AccessTokenValid 检测access_token是否有效
8. 自定义菜单管理
    + createCustomMenu 创建自定义菜单
    + getCustomMenu 获取自定义菜单
    + deleteCustomMenu 删除自动以菜单
9. 二维码接口
    + createQrCodeTicket 创建二维码ticket
    + getQrCode 根据ticket获取二维码
    + longUrl2ShortUrl 长链接转短链接
10. 统计数据
    + getSummary 获取统计数据
11. JS SDK
    + getJsTicket 获取jsapi_ticket
    + getJsSign 获取api配置参数