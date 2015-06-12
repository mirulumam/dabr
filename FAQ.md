This FAQ document will hopefully cover any questions you have regarding Dabr. If your question is not on the list then consider leaving a comment below, creating a new [Issue](http://code.google.com/p/dabr/issues/list), or just ask for some Dabr support on Twitter itself.



# FAQ for developers #

## How can I help the project? ##

Dabr is a hobby project of mine that I work on in my spare time. I'm happy to accept feature suggestions, patches, and any constructive comments. The project has been configured to allow anyone to submit code reviews - letting you quickly make comments about the code straight through this project site.

The original code is an uncommented mess, so I am particularly interested in any comments and suggestions you have about code in Dabr branches. It's here that Dabr is currently being re-written.

## How do I change the source "from dabr"? ##

Dabr is currently hard coded in twitter.php to always send out "from dabr" as the source. If you want to change this then, unfortunately, you must force users to use OAuth. According to [Twitter's FAQ](http://apiwiki.twitter.com/FAQ#HowdoIget“fromMyApp”appendedtoupdatessentfrommyAPIapplication), they no longer allow new sources to be added in the way that Dabr's works.

## I can't log in. The about and settings pages don't work ##

This is the most common problem that occurs with new server setups. Please ensure the following:

  * You have Apache mod\_rewrite installed
  * You have the .htaccess file supplied with dabr in the correct folder
  * You have Apache config "AllowOveride" set to ALL

## I'm getting errors about mcrypt ##

That's one of the requirements specified in the SetupGuide, your server needs to have mcrypt installed. Talk to your server administrator and ask them to install it.

## I only see one tweet per page ##

This was hopefully fixed in [r142](https://code.google.com/p/dabr/source/detail?r=142) of the code. It's because Twitter has _lots_ of tweets, more than 2147483647 of them in fact. That number is the limit of an integer on 32-bit systems and PHP can't count higher than that. Newer versions of Dabr treat those large numbers as strings instead.

# FAQ for users #

## What does Dabr mean? ##

Nothing. It was gibberish I made up so that I could get a short domain. It's pronounced dabber, which apparently is also the name for people who grew up in Nantwich (UK).

## I'm getting API errors! What's wrong? ##

More often than not, this means that Twitter is currently having problems. Dabr very rarely changes and hardly ever breaks. There are a handful of good places to check for more information that might hint why things are broken on their end:

  * http://status.twitter.com
  * http://twitter.com/TwitterAPI
  * http://groups.google.com/group/twitter-development-talk

## Can Dabr filter out Tweets that I don't want to see? ##

Short answer: no. If Dabr tried to filter out certain tweets or hashtags, it would merely show _less_ tweets, as opposed to more relevant tweets. If all your friends were tweeting about a filtered hashtag at once, you would see no tweets on the page at all - which is fairly useless.

The reason some other apps can do this is that they use a database on the server to store all your tweets. Dabr does not do this and always retrieves up-to-date information directly from Twitter.

## How do I retweet? ##

Retweet (or RT) is the quote icon, 4th one along.

## How do I delete a tweet? ##

Deleting can be done in two ways: either a) click the "trash" icon beside your tweet, which takes you to a confirmation screen, or b) click on the time beside your tweet to see the delete button without a confirmation screen.

## How long can a Direct Message be? ##

In the past you could DM up to 255, however Twitter now strictly enforce the 140 limit.

## Can Dabr expand short URLs? ##

For now, since Dabr has no server-side storage or caching, it would be slow down the site too much to add this feature. If you have your own copy of Dabr then take a look at [Expanding URLs in Dabr / Twitter](http://shkspr.mobi/blog/?p=461) on Terence Eden's Blog.