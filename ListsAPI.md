This List API documentation is simply a tidied up version of [an e-mail](http://groups.google.com/group/twitter-api-announce/browse_thread/thread/617bdef9f6b08372?hl=en). **The full API is now up at the official [Twitter API Wiki](http://apiwiki.twitter.com/Twitter-API-Documentation)**.



## Important PHP note ##

In my own testing, the PHP function `json_decode()` treats the 64-bit list paging cursors as _numbers_. This essentially breaks paging on 32-bit PHP installs and so I've switched to using XML and `simplexml_load_string()` as an alternative, which treats the 64-bit cursors as strings.

## List payload ##

```
<?xml version="1.0" encoding="UTF-8"?>
<list>
  <id>1416</id>
  <name>tall people</name>
  <full_name>@noradio/tall-people</full_name>
  <slug>tall-people</slug>
  <subscriber_count>0</subscriber_count>
  <member_count>3</member_count>
  <uri>/noradio/tall-people</uri>
  <mode>public</mode>
  <user>
    <id>3191321</id>
    <name>Marcel Molina</name>
    <screen_name>noradio</screen_name>
    <location>San Francisco, CA</location>
    <description>Engineer at Twitter on the @twitterapi team, obsessed
with rock climbing &amp; running. In a past life I was a member of the
Rails Core team.</description>
    <profile_image_url>http://a3.twimg.com/profile_images/53473799/marcel-euro-rails-conf_no...</profile_image_url>
    <url>http://project.ioni.st</url>
    <protected>false</protected>
    <followers_count>40059</followers_count>
    <profile_background_color>9AE4E8</profile_background_color>
    <profile_text_color>333333</profile_text_color>
    <profile_link_color>0084B4</profile_link_color>
    <profile_sidebar_fill_color>DDFFCC</profile_sidebar_fill_color>
    <profile_sidebar_border_color>BDDCAD</profile_sidebar_border_color>
    <friends_count>354</friends_count>
    <created_at>Mon Apr 02 07:47:28 +0000 2007</created_at>
    <favourites_count>131</favourites_count>
    <utc_offset>-28800</utc_offset>
    <time_zone>Pacific Time (US &amp; Canada)</time_zone>
    <profile_background_image_url>http://a1.twimg.com/profile_background_images/18156348/jessica_tiled....</profile_background_image_url>
    <profile_background_tile>true</profile_background_tile>
    <statuses_count>3472</statuses_count>
    <notifications>false</notifications>
    <geo_enabled>true</geo_enabled>
    <verified>false</verified>
    <following>false</following>
  </user>
</list>
```

## General Methods ##


### Create a list ###

POST '/:user/lists.:format'
Creates a new list for the authenticated user.

Parameters:
  * name: the name of the list. (required)
  * mode: whether your list is public of private. Values can be
'public' or 'private'. Public by default if not specified. (optional)

Usage notes:
> ":user" in the url should be the screen name of the user making the
request to create the list

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD -d "name=tall people&mode=private" http://twitter.com/noradio/lists.xml


### Update a list ###

POST/PUT '/:user/lists/:list\_slug.:format'

Takes the same parameters as the create resource at POST
'/:user/lists.:format' (:name and :mode).

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD -d "name=giants&mode=public" http://twitter.com/noradio/lists/tall-people.xml


### List your lists ###

GET '/:user/lists.:format'

Supported format:
xml, json

e.g.
> curl -u USERNAME:PASSWORD http://twitter.com/noradio/lists.xml


### List the lists the specified user has been added to ###

GET '/:user/lists/memberships.:format'

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD http://twitter.com/noradio/lists/memberships.xml


### Delete the specified list owned by the authenticated user ###

DELETE '/:user/lists/:list\_slug.:format'

Parameters:
  * list\_slug: the slug of the list you want to delete. (required)

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD -X DELETE http://twitter.com/noradio/lists/tall-people.xml


### Show tweet timeline for members of the specified list ###

GET '/:users/lists/:list\_slug/statuses.:format'

Parameters:
  * list\_slug: the slug of the list you want the member tweet timeline
of. (required)
  * next/previous\_cursor: used to "page" through results (optional)

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD http://twitter.com/noradio/lists/tall-people/statuses.xml


### Show a specific list you can use the new resource ###

GET '/:users/lists/:list\_slug.:format'

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD http://twitter.com/noradio/lists/tall-people.xml


## Member methods ##


### Add a member to a list ###

POST '/:user/:list\_slug/members.:format'

Parameters:
  * id: the id of the user you want to add as a member to the list. (required)

Usage notes:
The :list\_slug portion of the request path should be the slug of the
list you want to add a member to.

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD -d "id=123456789" http://http://twitter.com/noradio/tall-people/members.xml


### Members of the specified list ###

GET '/:user/:list\_slug/members.:format'

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD http://twitter.com/noradio/tall-people/members.xml


### Remove a member from the specified list ###

DELETE '/:user/:list\_slug/members.:format'

Parameters:
  * id: the id of the user you want to remove as a member from the
list. (required)

Usage notes:
The :list\_id portion of the request path should be the slug of the
list you want to add a member to.

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD -X DELETE -d "id=123456789" http://twitter.com/noradio/tall-people/members.xml


### Check if a user is a member of the specified list ###

GET '/:user/:list\_slug/members/:id.:format'

Usage notes:
The :id is the id of the user you're inquiring about.

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD http://twitter.com/noradio/tall-people/members/123456789.xml


## Subscribers methods ##


### Subscribe the authenticated user to the specified list ###

POST '/:user/:list\_slug/subscribers.:format'

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD -X POST http://twitter.com/noradio/tall-people/subscribers.xml


### List the users subscribed to the specified list ###

GET '/:user/:list\_slug/subscribers.:format'

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD http://twitter.com/noradio/tall-people/subscribers.xml


### Unsubscribe the authenticated user from the specified list ###

DELETE '/:user/:list\_slug/subscribers.:format'

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD -X DELETE http://twitter.com/noradio/tall-people/subscribers.xml


### Check if a user subscribes to the specified list ###

GET '/:user/:list\_slug/subscribers/:id.:format'

Usage notes:
The :id is the id of the user you're inquiring about.

Supported formats:
xml, json

e.g.
> curl -u USERNAME:PASSWORD http://twitter.com/noradio/tall-people/subscribers/123456789.xml