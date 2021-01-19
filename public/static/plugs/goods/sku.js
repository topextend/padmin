/**
 * Created by Administrator on 15-10-19.
 * 模拟淘宝SKU添加组合
 * 页面注意事项：
 *      1、 .sku_lists   这个类变化这里的js单击事件类名也要改
 *      2、 .titles      这个类作用是取到所有标题的值，赋给表格，如有改变JS也应相应改动
 *      3、 .items       这个类作用是取类型组数，有多少类型就添加相应的类名：如: items1、items2、items3 ...
 */
$(function() {

    blurPriceAmount();

    //SKU信息
    $("#sku_lists").find("li.sku-item").click(function() {
        getSkuTable($(this))
        /********编辑表格*******/
        step.table_sku_edit();
    })

    var step = {
        //SKU信息组合
        table_sku_edit: function() {
            step.hebingFunction();
            var obj_titles = $(".sku-group");
            var arrayInfor = new Array();//盛放每组选中的CheckBox值的对象
            var arrayColumn = new Array();//指定列，用来合并哪些列
            var bCheck = true;//是否全选
            var columnIndex = 0;

            $.each(obj_titles, function(i, item) {
                arrayColumn.push(columnIndex);
                columnIndex++;
                //选中的checkBox取值
                var order = new Array();

                $(".sku-group").eq(i).find("input.sku_id:checked").each(function() {
                    var obj_checkbox_checked = $(this);
                    var name_label = obj_checkbox_checked.parent().find(".labelname").text();
                    var name = obj_checkbox_checked.parent().find(".editbox").val();

                    var checked_id = obj_checkbox_checked.val();
                    var name_label_short = "";
                    if (name_label != name && name != undefined && name != '') {
                        name_label += "<span style='color:gray'>(" + name + ")</span>";
                        name_label_short = name;
                    }
                    name_label += "|" + checked_id + "|" + name_label_short;
                    order.push(name_label);
                });
                arrayInfor.push(order);
                //判断每行销售属性是否选中
                if (order.join() == "") {
                    bCheck = false;
                }
            });

            //替换#tables_tbody_sku
            if (bCheck == true) {
                var RowsCount = 0;
                ////生成组合
                // console.log(arrayInfor);
                if (arrayInfor.length > 0) {
                    var zuheDate = step.doExchange(arrayInfor);
                    if (zuheDate.length > 0) {
                        //创建行
                        var tr = "";
                        var sku_default = $("#tables_tbody_sku").attr("data-default");
                        $.each(zuheDate, function(index, item) {
                            var td_array = item.split(",");

                            var td_sku_value = "";
                            var tr_sku_cla = "sku_";
                            var sku_ids = "";
                            var sku_names = "";
                            var sku_names_short = "";
                            $.each(td_array, function(i, values) {
                                var valuesArr = values.split("|");

                                td_sku_value += "<td width='15%' class='text-center td_sku_" + valuesArr[1] + "'>" + valuesArr[0] + "</td>";
                                tr_sku_cla += valuesArr[1] + "_";
                                sku_ids += valuesArr[1] + "_";
                                sku_names += valuesArr[0] + "_";
                                if (valuesArr[2] == '') {
                                    valuesArr[2] = valuesArr[0];
                                }
                                sku_names_short += valuesArr[2] + "_";
                            });
                            sku_ids = sku_ids.substring(0, sku_ids.length - 1);
                            sku_names = sku_names.substring(0, sku_names.length - 1);

                            sku_names_short = sku_names_short.substring(0, sku_names_short.length - 1);
                            tr_sku_cla = tr_sku_cla.substring(0, tr_sku_cla.length - 1);
                            var price = $("#price").val();
                            tr += "<tr class='" + tr_sku_cla + "'>" + td_sku_value;
                            var obj_goods_sku_input_price = $("#goods_sku_input").children(".price_" + tr_sku_cla);
                            var input_price = obj_goods_sku_input_price.val();
//                        $(".preview").html(tr_sku_cla+"|"+obj_goods_sku_input_price.length);
                            if (input_price != '' && input_price != undefined && obj_goods_sku_input_price.length > 0) {
                                price = input_price;
                            }
                            price = price == undefined ? '' : price;
                            var obj_goods_sku_input_amount = $("#goods_sku_input").children(".amount_" + tr_sku_cla);
                            var input_amount = obj_goods_sku_input_amount.val() != undefined ? obj_goods_sku_input_amount.val() : "";
                            var checked = "";
                            if (sku_ids == sku_default) {
                                checked = "checked";
                            }
                            tr += "<td class='text-center'><input type='hidden' name='sku_ids[]' value='" + sku_ids + "' class='sku_ids'>\n\
<input type='hidden' name='sku_names[]' value='" + sku_names_short + "' class='sku_names'><input type='text' name='sku_price[]' value='" + price + "' class='price text'></td>\n\
<td class='text-center'><input type='text' name='sku_amount[]' value='" + input_amount + "' class='amount text' maxlength='9'></td>";
                        });
                    }
                }
                $("#tables_tbody_sku").html(tr);
                //结束创建Table表
                arrayColumn.pop();//删除数组中最后一项
                //合并单元格
                $("#tables_sku").mergeCell({
                    // 目前只有cols这么一个配置项, 用数组表示列的索引,从0开始
                    cols: arrayColumn
                });
            } else {
                //未全选中,清除表格
                $("#tables_tbody_sku").empty();
            }
            updatePriceAmount();
            showSkuTable();
        }, //合并行
        hebingFunction: function() {
            $.fn.mergeCell = function(options) {
                return this.each(function() {
                    var cols = options.cols;
                    for (var i = cols.length - 1; cols[i] != undefined; i--) {
                        // fixbug console调试
                        // console.debug(cols[i]);
                        mergeCell($(this), cols[i]);
                    }
                    dispose($(this));
                });
            };
            function mergeCell($table, colIndex) {
                $table.data('col-content', ''); // 存放单元格内容
                $table.data('col-rowspan', 1); // 存放计算的rowspan值 默认为1
                $table.data('col-td', $()); // 存放发现的第一个与前一行比较结果不同td(jQuery封装过的), 默认一个"空"的jquery对象
                $table.data('trNum', $('tbody tr', $table).length); // 要处理表格的总行数, 用于最后一行做特殊处理时进行判断之用
                // 进行"扫面"处理 关键是定位col-td, 和其对应的rowspan
                $('tbody tr', $table).each(function(index) {
                    // td:eq中的colIndex即列索引
                    var $td = $('td:eq(' + colIndex + ')', this);
                    // 取出单元格的当前内容
                    var currentContent = $td.html();
                    // 第一次时走此分支
                    if ($table.data('col-content') == '') {
                        $table.data('col-content', currentContent);
                        $table.data('col-td', $td);
                    } else {
                        // 上一行与当前行内容相同
                        if ($table.data('col-content') == currentContent) {
                            // 上一行与当前行内容相同则col-rowspan累加, 保存新值
                            var rowspan = $table.data('col-rowspan') + 1;
                            $table.data('col-rowspan', rowspan);
                            // 值得注意的是 如果用了$td.remove()就会对其他列的处理造成影响
                            $td.hide();
                            // 最后一行的情况比较特殊一点
                            // 比如最后2行 td中的内容是一样的, 那么到最后一行就应该把此时的col-td里保存的td设置rowspan
                            if (++index == $table.data('trNum'))
                                $table.data('col-td').attr('rowspan', $table.data('col-rowspan'));
                        } else { // 上一行与当前行内容不同
                            // col-rowspan默认为1, 如果统计出的col-rowspan没有变化, 不处理
                            if ($table.data('col-rowspan') != 1) {
                                $table.data('col-td').attr('rowspan', $table.data('col-rowspan'));
                            }
                            // 保存第一次出现不同内容的td, 和其内容, 重置col-rowspan
                            $table.data('col-td', $td);
                            $table.data('col-content', $td.html());
                            $table.data('col-rowspan', 1);
                        }
                    }
                });
            }
            // 同样是个private函数 清理内存之用
            function dispose($table) {
                $table.removeData();
            }
        },
        //组合数组
        doExchange: function(doubleArrays) {
            var len = doubleArrays.length;
            if (len >= 2) {
                var arr1 = doubleArrays[0];
                var arr2 = doubleArrays[1];
                var len1 = doubleArrays[0].length;
                var len2 = doubleArrays[1].length;
                var newlen = len1 * len2;
                var temp = new Array(newlen);
                var index = 0;
                for (var i = 0; i < len1; i++) {
                    for (var j = 0; j < len2; j++) {
                        temp[index] = arr1[i] + "," + arr2[j];
                        index++;
                    }
                }
                var newArray = new Array(len - 1);
                newArray[0] = temp;
                if (len > 2) {
                    var _count = 1;
                    for (var i = 2; i < len; i++) {
                        newArray[_count] = doubleArrays[i];
                        _count++;
                    }
                }
                return step.doExchange(newArray);
            }
            else {
                return doubleArrays[0];
            }
        }
    }
    step.table_sku_edit();
//    return step;

})


function blurPriceAmount()
{
    $("#tables_tbody_sku").on("blur", ".price", function()
    {
        var cla = "price_" + $(this).parents("tr").attr("class");
        var val = $(this).val();
        if (val != '' && val != undefined) {
            if ($("#goods_sku_input").find("." + cla).length == 0) {
                $("#goods_sku_input").append("<input class='" + cla + "' type='hidden' value='" + val + "'>");
            } else {
                $("#goods_sku_input").find("." + cla).val(val);
            }
        }
    })
    $("#tables_tbody_sku").on("blur", ".amount", function() {
        var cla = "amount_" + $(this).parents("tr").attr("class");
        var val = $(this).val();
        if (val != '' && val != undefined) {
            if ($("#goods_sku_input").find("." + cla).length == 0) {
                $("#goods_sku_input").append("<input class='" + cla + "' type='hidden' value='" + val + "'>");
            } else {
                $("#goods_sku_input").find("." + cla).val(val);
            }
        }
    })
}
function updatePriceAmount()
{
    $("#tables_tbody_sku").children("tr").each(function() {
        var obj_tr = $(this);
        var cla_tr = obj_tr.attr("class");
        var price = obj_tr.find(".price").val();
        var amount = obj_tr.find(".amount").val();
        var cla_price = "price_" + cla_tr;
        var cla_amount = "amount_" + cla_tr;
        if (price != '' && price != undefined) {
            if ($("#goods_sku_input").find("." + cla_price).length == 0) {
                $("#goods_sku_input").append("<input class='" + cla_price + "' type='hidden' value='" + price + "'>");
            } else {
                $("#goods_sku_input").find("." + cla_price).val(price);
            }
        }
        if (amount != '' && amount != undefined) {
            if ($("#goods_sku_input").find("." + cla_amount).length == 0) {
                $("#goods_sku_input").append("<input class='" + cla_amount + "' type='hidden' value='" + amount + "'>");
            } else {
                $("#goods_sku_input").find("." + cla_amount).val(amount);
            }
        }
    })
}
function getSkuTable(obj)
{
    /*******显示颜色备注******/
    var sku_id = obj.find(".sku_id").val();
    var sku_names = obj.find(".editbox").val();
    var checked = obj.children("input[type=checkbox]").prop("checked");
    var sku_parent_code = obj.parent().attr("data_sku_parent_code");
    var color_name = obj.find(".labelname").text();
    var color_bg = obj.find(".color-lump").attr("data-bgcolor");

    if (checked == true) {
        obj.children(".labelname").hide();
        obj.children(".editbox").show();
        if (sku_parent_code == 'color') {
            var url_action = $('#goods_sku_input').attr("data-url");
            var tr_img = "<tr id='sku_upload_" + sku_id + "'><td class='tile'>\n\
        <span  class='td_sku_" + sku_id + "'>" + sku_names + "</span><input type='hidden' name='sku_upload_ids[]' value='" + sku_id + "'><input type='hidden' name='sku_upload_names[]' value='" + sku_names + "'></td>\n\
    <td class='text-center image'>\n\
        <form action='" + url_action + "' enctype='multipart/form-data' method='post' class='imageform'>\n\
            <div class='btn_upload'>\n\
                <span>添加图片</span>\n\
                <input type='file' name='file' class='photoimg'>\n\
            </div>\n\
        </form>\n\
        <div class='preview_img'><img alt='uploading' src='/static/theme/img/404_icon.png'></div>\n\
    </td>\n\
</tr>";
            if ($("#sku_upload_" + sku_id + "").length == 0) {
                $("#table_color_upload").children("tbody").append(tr_img);
            }
        }
    } else {
        obj.children(".labelname").show();
        obj.children(".editbox").hide();
        if (sku_parent_code == 'color') {
            $("#table_color_upload").find("#sku_upload_" + sku_id + "").remove();
        }
    }
}
function showSkuTable() {
    /********判断是否选中每列销售属性*******/
    var sku_chose_no = 0;
    $(".sku-list").each(function() {
//            alert($(this).html())
        if ($(this).find(".sku_id:checked").length == 0) {
            sku_chose_no++;
        }
    })
//    alert(sku_chose_no);
    if (sku_chose_no > 0) {
        $("#msg-attention").show();
        // $("#tables_sku").hide();
    } else {
        $("#msg-attention").hide();
        // $("#tables_sku").show();
    }
}

