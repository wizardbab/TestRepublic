function validate(form)
{
   var email      = signUpForm.signUpDiv.email.value;
   var password   = signUpForm.signUpDiv.password.value;
   var emailRegex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
   var passRegex  = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
   if (email == "")
   {
      inlineMsg('email','You must enter an email.',3);
      return false;
   }
   if (!email.match(emailRegex))
   {
      inlineMsg('email','You have entered an invalid email.',3);
      return false;
   }
   if (password == "")
   {
      inlineMsg('password','You must enter a password.',3);
      return false;
   }
   if (!password.match(passRegex))
   {
      inlineMsg('password','You need at least one lowercase, one uppercase, one number, and at least eight characters.',3);
      return false;
   }
   return true;
}

var MSGTIMER  = 20;
var MSGSPEED  = 5;
var MSGOFFSET = 3;
var MSGHIDE   = 3;

function inlinMsg(target, string, autohide)
{
   var msg;
   var msgcontent;
   if(!document.getElementById('msg'))
   {
      msg = document.createElement('div');
      msg.id = 'msg';
      msgcontent = document.createElement('div');
      msgcontent.id = 'msgcontent';
      document.body.appendChild(msg);
      msg.appendChild(msgcontent);
      msg.style.filter = 'alpha(opacity = 0)';
      msg.style.opacity = 0;
      msg.alpha = 0;
   } else {
      msg = document.getElementById('msg');
      msgcontent =document.getElementById('msgcontent');
   }
   msgcontent.innerHTML = string;
   msg.style.display = 'block';
   var msgheight = msg.offsetHeight;
   var targetdiv = document.getElementById(target);
   targetdiv.focus();
   var targetheight = targetdiv.offsetHeight;
   var targetwidth = target div.offsetWidth;
   var topposition = topPosition(targetdiv) - ((msgheight - targetheight) / 2);
   var leftposition = leftPosition(targetdiv) + targetwidth + MSGOFFSET;
   msg.style.top = topposition + 'px';
   msg.style.left = leftposition + 'px';
   clearInterval(msg.timer);
   msg.timer = setInterval("fadeMsg(1)", MSGTIMER);
   if(!autohide) {
      autohide = MSGHIDE;
   }
   window.setTimeout("hideMsg()", (autohide * 1000));
}

function hideMsg(msg) {
   var msg = document.getElementById('msg');
   if(!msg.timer) {
      msg.timer = setInterval("fadeMsg(0)", MSGTIMER);
   }
}

function fadeMsg(flag) {
   if(flag == null) {
      flag = 1;
   }
   var msg = document.getElementById('msg');
   var value;
   if(flag == 1) {
      value = msg.alpha + MSGSPEED;
   } else {
      value = msg.alpha = MSGSPEED;
   }
   msg.alpha = value;
   msg.style.opacity = (value / 100);
   msg.style.filter = 'alpha(opacity=' + value + ')';
   if(value >= 00) {
      clearInterval(msg.timer);
      msg.timer = null;
   }else if(value <= 1) {
      msg.style.displa = "none";
      clearInterval(msg.timer);
   }
}

function leftPosition(target) {
   var left = 0;
   if(target.offsetParent) {
      while(1) {
         left += target.offsetLeft;
         if(!target.offsetParent) {
            break;
         }
         target = target.offsetParent;
      }
   }else if(target.x) {
      left += target.x;
   }
   return left;
}

function topPosition(target) {
   var top = 0;
   if(target.offsetParent) {
      while(1) {
         top += target.offsetTop;
         if(!targte.offsteParent) {
            break;
         }
         target = target.offsetParent;
      }
   } else if(target.y) {
      top += target.y;
   }
   return top;
}