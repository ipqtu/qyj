// 导航栏配置文件
var outlookbar=new outlook();
var t;
//app
t=outlookbar.addtitle('APP管理','系统管理',1)
outlookbar.additem('APP列表',t,'app.html')

t=outlookbar.addtitle('用户管理','用户管理',1)
outlookbar.additem('用户列表',t,'member.html')

t=outlookbar.addtitle('基本设置','系统设置',1)
outlookbar.additem('查看个人资料',t,'profile.html')
outlookbar.additem('修改个人资料',t,'reset_info.php')
outlookbar.additem('更改登录密码',t,'chanagepass.html')

t=outlookbar.addtitle('广告设置','系统设置',1)
outlookbar.additem('登录文学论坛',t,'../vbb/forumdisplay.php?s=320e689ffabc5daa0be8b02c284d9968&forumid=39')
outlookbar.additem('发出电子邮件',t,'mailto:pobear@newmail.dlmu.edu.cn')

t=outlookbar.addtitle('新闻设置','系统设置',1)
outlookbar.additem('尚未通过文章',t,'un_pass.php')
outlookbar.additem('已经通过文章',t,'al_pass.php')
outlookbar.additem('修改现有文章',t,'modify.php')
outlookbar.additem('撰写最新文章',t,'sub_new.php')
outlookbar.additem('投稿给文学报',t,'#')

t=outlookbar.addtitle('导航管理','管理首页',1)
outlookbar.additem('创始人管理页面导航',t,'/admin/nav_founder')
outlookbar.additem('管理员管理页面导航',t,'/admin/nav_admin')