CKEDITOR.plugins.add( 'createNode',
{
	requires : [ 'styles', 'button' ],
        icons:'createNode',
        
	init : function( editor )
	{
             var text = " ";
             
            //Node Title
            editor.ui.addButton( 'NT',
            {
              label: 'Node Title',
              command: 'NT',
              icon: this.path + 'images/NT.png',
              toolbar: 'createNode'
            });
            editor.addCommand( 'NT', {
                exec : function () {
                        text = editor.getSelection().getSelectedText();
                        editor.insertHtml('<strong>NTB</strong>.'+ text +'<strong>NTE</strong>.');

                }
            });
            //Node body
            editor.ui.addButton( 'NB',
            {
              label: 'Node body',
              command: 'NB',
              icon: this.path + 'images/NB.png',
              toolbar: 'createNode'
            });
            editor.addCommand( 'NB', {
                exec : function () {
                        text = editor.getSelection().getSelectedText();
                        editor.insertHtml('<strong>NBB</strong>.'+ text +'<strong>NBE</strong>.');

                }
            });
	}
});
