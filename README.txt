Ckeditor_plugin_createNode

With this Drupal module you can mark in a body the title and content for a new automaticly created node.

Change the User rights:
    After activating this module you have to change the ckeditor settings. 
    ckeditor settings: admin/config/content/ckeditor
        In the profiles you can find the button ('NT','NB') in de map EDITOR APPEARANCE.
        You can move the button from 'Available buttons' to 'Current toolbar' 
        After saving the settings the user will be able to see the buttons.
        It is possible you will have the flush the cache. 


NT button: Select in de body 1 or more words and with this button it wil be markt als title.
NB button: Select in de body 0 or more words and with this button it wil be markt als bodytext.

Created node type 'article'
    The new node will be created with type 'article'.
    In the function public function findNodeId($content) (see ckeditor_plugin_createNode_ckeditorPlugin.class.php)
    you can change the type you wish to use.

Rules:
- The selections of the new title/bodytext combination the search go from top to botum.
- After the title selection this module expect to find the bodytext.
- Do not make a title and bodytext selection in a bodytext selection.

Error messages:
    If the system find more then one node with the same title it will give a list with found
    node titles and request the user to change the title selections.

Result:
    The title will be changed into a link.
    The body text will be removed.
    If the selected title is not found in the excisting list of nodes. It will be created.
    If the title already excists the body selection will be added to the excisting node.
