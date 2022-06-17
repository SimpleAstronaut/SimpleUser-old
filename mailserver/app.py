'''
-----------------------------------------------------
请注意，这是一个已经弃用的代码!
-----------------------------------------------------

Simple User v1.0 MailServer
通过http请求通过ssl的smtp协议连接远程SMTP服务器并发送邮件

需要flask库支持

Powered by SimpleAstronaut
2022-1-24

------------------------------------------------------

由于由于1.0版本中暴露出的诸多问题,我推翻了之前使用php和py开发的底层,全部重新编写后端服务器，该版本邮件服务器也随之下线弃用。
1.0版本开发之初作者水平有限,导致代码冗长复杂,代码规范问题显著,仅供参考

SimpleAstronaut
2022-6-17
'''

# coding=UTF-8
from quopri import decodestring, encodestring
from flask import Flask, redirect, url_for, request
from flask_mail import Mail, Message

app =Flask(__name__)
mail=Mail(app)

app.config['MAIL_SERVER']=''
app.config['MAIL_PORT'] = 465
app.config['MAIL_USERNAME'] = ''
app.config['MAIL_PASSWORD'] = ''
app.config['MAIL_USE_TLS'] = False
app.config['MAIL_USE_SSL'] = True
mail = Mail(app)

@app.route('/sendmsg',methods = ['GET'])
def index():
   target = str(request.args.get('target').encode('utf-8').decode('utf-8'))
   sendmsg = "你的验证码是 "+request.args.get('msg')
   sendmsg = sendmsg.encode('utf-8'). decode('utf-8') 
   msg = Message('简单云账号验证码', sender = '', recipients = [target])
   msg.body = sendmsg
   mail.send(msg)
   return "Sent"

if __name__ == '__main__':
   app.run()