define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'base/promotion/index' + location.search,
                    add_url: 'base/promotion/add',
                    edit_url: 'base/promotion/edit',
                    del_url: 'base/promotion/del',
                    multi_url: 'base/promotion/multi',
                    table: 'base_promotion',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'promotion_id',
                sortName: 'promotion_id',
                columns: [
                    [
                        {checkbox: true},
                       
                        {field: 'promotion', title: __('Promotion')},
                     
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});