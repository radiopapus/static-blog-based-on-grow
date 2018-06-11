---
$title@: Ext4js-podskazki-dlya-yacheek-grida
author@: Viktor Zharina
$order: 220
$dates:
  published: 2015-06-09 09:39:14
---
Не работает для случаев, когда вы переопределили renderer для columns

<code>

Ext.tip.QuickTipManager.init();



Ext.create('Ext.data.Store', {

    storeId:'simpsonsStore',

    fields:['name', 'email', 'phone'],

    data:{'items':[

        { 'name': 'Lisa',  "email":"lisa@simpsons.com",  "phone":"555-111-1224"  },

        { 'name': 'Bart',  "email":"bart@simpsons.com",  "phone":"555-222-1234" },

        { 'name': 'Homer', "email":"home@simpsons.com",  "phone":"555-222-1244"  },

        { 'name': 'Marge', "email":"marge@simpsons.com", "phone":"555-222-1254"  }

    ]},

    proxy: {

        type: 'memory',

        reader: {

            type: 'json',

            root: 'items'

        }

    }

});



Ext.create('Ext.grid.Panel', {

    title: 'Simpsons',

    store: Ext.data.StoreManager.lookup('simpsonsStore'),

    columns: [

        { text: 'Name',  dataIndex: 'name' },

        { text: 'Email', dataIndex: 'email', flex: 1 },

        { text: 'Phone', dataIndex: 'phone' }

    ],

    height: 200,

    width: 400,

    renderTo: Ext.getBody()

    , listeners: {

        viewready: function (grid) {

            var view = grid.view;

            

            // record the current cellIndex

            grid.mon(view, {

                uievent: function (type, view, cell, recordIndex, cellIndex, e) {

                    grid.cellIndex = cellIndex;

                    grid.recordIndex = recordIndex;

                }

            });

            

            grid.tip = Ext.create('Ext.tip.ToolTip', {

                target: view.el,

                delegate: '.x-grid-cell',

                trackMouse: true,

                renderTo: Ext.getBody(),

                listeners: {

                    beforeshow: function updateTipBody(tip) {

                        if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {

                            header = grid.headerCt.getGridColumns()[grid.cellIndex];

                            tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));

                        }

                    }

                }

            });



        }

    }

});

</code>