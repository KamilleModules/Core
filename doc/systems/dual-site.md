Dual Site
================
2017-05-18




One of the coolest feature provided by the Core module is the ability to turn a single website into a dual website.

A dual website is composed of two websites: the front and the back (aka admin aka backoffice).


This feature is kool because the uri leading to the admin is virtual, and so we can change it on the fly,
hence potentially helping creating a better security model.



However, handling two websites with only one application root also comes with a big problem:

how do you know whether or not you are on the front or on the back?

If you think about php sessions, that sounds complicated, because you have to keep track of where you are in 
your application code at any time, and it sounds like you will have sync problems sooner or later.
Http is a stateless protocol, and so sessions are already an extra layer of complexity.

So, following this logic, the only solution we are left with (or at least that I found) is to rely on an uri prefix.

For instance, if the uri starts with **/admin**, then we are on the backoffice, otherwise we are on the front.

That system works well, and we can change **/admin** to whatever we want so it's flexible, **BUT³**...
 
But then there are ajax services which possibly display some visual things.

For instance:

- the DataTable module provides the whole datatable via ajax
- the GscpModalResponse provides uses a modal content widget via ajax


When something displays a visual thing, assuming we use MVC, we need to know the theme.

So, is the theme the front office theme, or the backoffice theme?

Since it's ajax, it doesn't belong to the front or the back, at least semantically.


And the whole point of this discussion is coming right now: by default, an ajax service naturally isn't categorized
in either the front or the back category.

HOWEVER, (dualSite rule for ajax service)
 
- if the ajax service needs to DISPLAY something, then you need to pick up a side, that means to choose whether it's a backoffice service, or a front office service.
            Or to be more precise, whether the theme displaying your ajax response will be the backoffice theme or the front theme.
            
            
Implementation wise, the Core module provides a mechanism that allows you to do that:
there are two types of routes (by default) with dualSites, in two different files:
            
- routes.php: routes for the front
- back.php: routes for the back


SO, if you have a service which output should be displayed by the backoffice, put it in the back.php file.
If the same service is used both by the back and the front, create two routes (but you will have to change one route's name though,
sorry, that's the flaw of this design), one in the back and one in the front.


Get it?





Implementation details
=========================
2017-06-07

The switching mechanism is created in X::Core_RoutsyRouter by the Core module.
Basically, thanks to the RoutsyRouter capabilities, when a route match, we are able to set the theme,
depending on which file the route was attached to.


Absolute urls?
------------------
Now since we are using a dual site, the host name is the same (by definition).
But what about the protocol: http or https?

I believe in the end the user (writing the link) should always be able
to decide the protocol.
However, most of the time your whole website will either be https, or it will not,
so it might be cool if we could just have a default value.

So, we provide the defaultProtocol configuration key for that.









