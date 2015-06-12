## The problem: ##

> 400: **Rate Limit exceeded, clients may not make more that 150 requests per hour.**

## The fix: ##

  1. Log out of Dabr
  1. Click "Sign in with Twitter"
  1. Enjoy a new API limit of 350!

## Information: ##

Twitter imposes an hourly limit on how often Dabr can ask for information on your behalf. If you go over this limit then Twitter will stop talking with Dabr for a while.

Using the "Sign in with Twitter" method described above uses what's called "OAuth" authentication, which Twitter have decided should allow you to have an increased API usage limit. This method is also slightly more secure than trusting third party websites not to abuse your login details, so I'd recommend all users to try it.

Happy tweeting :)