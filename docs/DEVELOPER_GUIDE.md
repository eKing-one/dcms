# CN_DCMS-Social 开发文档

本开发文档记录了项目中关键的文件、函数及其位置和作用，便于开发人员快速查找相关代码和功能。

## 文件 `sys/inc/fnc.php`

- **内容**：该文件包含了项目中的大部分可供调用的函数。

---

## 函数 `img_copyright`

- **来源**：`sys/fnc/img_copyright.php`
- **作用**：为图片添加水印。

---

## 函数 `img_preg`

- **来源**：`sys/fnc/links.php`
- **作用**：处理并渲染 `[img]` 标签的内容。

---

## 函数 `err`

- **来源**：`sys/inc/fnc.php`
- **作用**：渲染报错横幅。
- **用法**：

  ```php
  $err = '报错内容';
  err();
  ```

---

## 函数 `msg`

- **来源**：`sys/inc/fnc.php`
- **作用**：渲染提示横幅。
- **用法**：

  ```php
  msg('信息内容');
  ```

---

## 函数 `save_settings`

- **来源**：`sys/inc/fnc.php`
- **作用**：修改并保存配置文件 `sys/dat/settings.php`。
- **用法**：

  ```php
  save_settings($set); // $set 是包含要更改设置信息的数组
  ```

- **示例**：

  ```php
  global $set;                // 获取全局设置数组 $set
  $temp_set = $set;           // 复制 $set 到临时变量 $temp_set
  $temp_set['bb_i'] = 1;      // 修改临时变量中的设置
  save_settings($temp_set);   // 保存修改后的设置
  ```

---

## 函数 `admin_log`

- **来源**：`sys/inc/fnc.php`
- **作用**：记录管理员的操作日志。
- **用法**：

  ```php
  admin_log('日志类型', '日志目标', '日志内容');
  ```

- **示例**：

  ```php
  admin_log('设置', '系统', '更改BBCode参数');
  ```

---

## 全局变量 `set`

- **来源**：`sys\inc\settings.php`
- **作用**：获取配置文件 `sys/dat/settings.php` 的配置内容。
- **用法**：

  ```php
  global $set;
  echo $set['所需的配置项'];
  ```

---

## 变量 `ip`

- **来源**：`sys\inc\ipua.php`
- **作用**：获取用户 IP 地址

---

## 文件 `sys\dat\cloudflare-ips-v4.txt`

- **内容**：Cloudflare IPv4 列表

---

## 文件 `sys\dat\cloudflare-ips-v6.txt`

- **内容**：Cloudflare IPv6 列表

---

## 函数 `getLatestStableRelease`

- **来源**：`sys/inc/fnc.php`
- **作用**：从GitHub仓库获取最新版CN_DCMS-Social和更新包信息。
- **用法**：

  ```php
  $result = getLatestStableRelease();
  echo "Latest Stable Version: " . $result['version'] . PHP_EOL;
  echo "ZIP Download URL: " . $result['zip_url'] . PHP_EOL;
  ```
