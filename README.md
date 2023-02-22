> 此仓库是自用修改版本，复刻自 https://github.com/Hehongyuanlove/flarum-auth-qq

# QQ Auth Login by imeepo

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/imeepo/flarum-auth-qq.svg)](https://packagist.org/packages/imeepo/flarum-auth-qq)

A [Flarum](http://flarum.org) extension. Allow users to log in with QQ

### 申请开通QQ互联
#### 注册开发者账号并实名认证
https://wikinew.open.qq.com/index.html#/iwiki/863406134
#### 创建网站应用
https://connect.qq.com/manage.html
```text
网站地址：https://你的域名/
网站回调域：https://你的域名/auth/qq;https://你的域名/auth/qq/link
```
提交后等待审核，大概1个工作日左右吧

### 安装
```sh
composer require imeepo/flarum-auth-qq
# 有兼容提示就
composer require imeepo/flarum-auth-qq:*
```

### 更新
```sh
composer update imeepo/flarum-auth-qq
```

### Links
- [Packagist](https://packagist.org/packages/imeepo/flarum-auth-qq)
