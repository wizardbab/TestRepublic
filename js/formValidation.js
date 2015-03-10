// Function that validates the form
function validate(form)
{
   var MESSAGE_DURATION = 3;
   var firstName        = form.firstName.value;
   var lastName         = form.lastName.value;
   var email            = form.email.value;
   var password         = form.password.value;
   var emailRegex       = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
   var passwordRegex    = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
   
   if(firstName == "")
   {
      inlineMsg('firstName', 'Your first name cannot be blank', MESSAGE_DURATION);
      return false;
   }
   if(lastName == "")
   {
      inlineMsg('lastName', 'Your last name cannot be blank', MESSAGE_DURATION);
      return false;
   }
   if(email == "")
   {
      inlineMsg('email', 'Your email cannot be blank', MESSAGE_DURATION);
      return false;
   }
   if(!email.match(emailRegex))
   {
      inlineMsg('email', 'Your email is invalid' MESSAGE_DURATION);
      return false;
   }
   if(password == "")
   {
      inlineMsg('password', 'Your password cannot be blank', MESSAGE_DURATION);
      return false;
   }
   if(!password.match(passwordRegex))
   {
      inlineMsg('password', 'Your password must be at least eight characters long, have one number, one uppercase, and one lowercase letter', MESSAGE_DURATION);
      return false;
   }
   return true;
}

// The creation of the message itself as it is shown on the page begins here.
var MSGTIMER  = 20;
var MSGSPEED  = 5;
var MSGOFFSET = 3;
var MSGHIDE   = 3;

function inlineMsg(target_id, error_string, autohide_length)
{
   var message;
   var message_content;
   
   if(!document.getElementById('message'))
   {
      message = document.createElement('div');
      message.id            = 'message';
      message_content       = document.createElement('div');
      message_content.id    = 'message_content';
      document.body.appendChild(message);
      message.appendChild(message_content);
      message.style.filter  = 'alpha(opacity = 0)';
      message.style.opacity = 0;
      message.alpha         = 0;
   }
   else
   {
      message         = document.getElementById('message');
      message_content = document.getElementById('message_content');
   }
   message_content.innerHTML = error_string;
   message.style.display = 'block';
   var message_height = message.offsetHeight;
   var target_div = document.getElementById(target_id);
   target_div.focus();
   var target_height = target_div.offsetHeight;
   var target_width = target_div.offsetWidth;
   var top_position = topPosition(target_div) - ((message_height - target_height) / 2);
   var left_position = leftPosition(target_div) + target_width + MSGOFFSET;
   message.style.top = top_position + 'px';
   message.style.eleft = left_position + 'px';
   clearInterval(message.timer);
   message.timer = setInterval("fade_message(1)", MSGTIMER);
   if(!autohide_length)
      autohide_length = MSGHIDE;
   window.setTimeout("hide_message()", (autohide_length * 1000));
}

function hide_message(message)
{
   var message = document.getElementById('message');
   if(!message.timer)
      message.timer = setInterval("fade_message(0)", MSGTIMER);
}

function fade_message(flag)
{
   if(flag == null)
      flag = 1;
   var message = document.getElementById('message');
   var value;
   if(flag == 1)
      value = message.alpha + MSGSPEED;
   else
      value = message.alpha - MSGSPEED;
   message.alpha = value;
   message.style.opacity = (value / 100);
   message.style.filter = 'alpha(opacity = ' + value + ')';
   if(value >= 99)
   {
      clearInterval(message.timer);
      message.timer = null;
   }
   else if(value <= 1)
   {
      message.style.display = "none";
      clearInterval(message.timer);
   }
}

function leftPosition(target)
{
   var left = 0;
   if(target.offsetParent)
   {
      while(true)
      {
         left += target.offsetLeft;
         if(!target.offsetParent)
            break;
         target = target.offsetParent;
      }
   }
   else if(target.x)
      left += target.x;
   return left;
}

function topPosition(target)
{
   var top = 0;
   if(target.offsetParent)
   {
      while(true)
      {
         top += target.offsetTop;
         if(!target.offsetParent)
            break;
         target = target.offsetParent;
      }
   }
   else if(target.y)
      top += target.y;
   return top;
}

if(document.images)
{
   arrow = new Image(7,80);
   arrow.src = "../images/msg_arrow.gif";
}