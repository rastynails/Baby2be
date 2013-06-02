<?php

class field_username_suggest extends SK_FormField
{
    public $items = array();
    
    public $labelsection;
    
    public $responder_action;
    
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct( $name = 'username_suggest' ) 
    {
        parent::__construct($name);
    }
    
    public function setResponderAction( $responder_action )
    {
        $this->responder_action = $responder_action;
    }
    
    public function setup(SK_Form $form){
        
        $this->js_presentation['$parent_node']='{}';
        $this->js_presentation['$container_node']='{}';
        
        $this->js_presentation['fields']='[]';
                
        $this->js_presentation['$prototype_node']='{}';
        
        $this->js_presentation['construct']='
            function($input){
                handler = this;
                this.$parent_node = $input.parents("tbody:eq(0)");
                this.$container_node = $input.parents(".username_container:eq(0)");             
                this.suggest(this.$parent_node.find("input[name*=user_name]"));              
            } 
        ';
        
        $this->js_presentation['ajax_receiver']='
            function(result){
                handler = this;
                            
                $.each(result.hide_items,function(i,item){
                    handler.$parent_node.find("tr."+item).css("display","none");
                    handler.$parent_node.find("tr."+item+" td.val").empty();
                });
                
                $.each(result.assign,function(item,html){
                    var $node = handler.$parent_node.find("tr."+item+" td.val");
                    $node.empty();
                    $node.append(html);
                    
                    $node.find("input, select").each(function(){
                        var name = $(this).attr("name");
                        if (name!=undefined){
                            $(this).attr("name","'.$this->getName().'["+name+"]");
                        }
                    });
                });
                                
                $.each(result.show_items,function(i,item){
                    handler.$parent_node.find("tr."+item).fadeIn("slow");
                }); 
            }
        ';
        
        $this->js_presentation['$'] = 'function($expr){
            return this.$container_node.find($expr);
        }';
        
        $this->js_presentation['$suggest_cont_prototype'] = 'undefined';
        
        $this->js_presentation['showSuggest'] = 'function($node, suggests){
            var handler = this;
            
            var removeSuggest = function(){
                $node.parent().siblings(".suggest_cont").remove();
            }
            
            if (this.$suggest_cont_prototype ==undefined) {
                this.$suggest_cont_prototype = this.$(".suggest_cont").remove().clone();
            }       
            
            var $suggest_cont = this.$suggest_cont_prototype.clone();
            
            removeSuggest();
            
            if (suggests.length <= 0) {
                return;
            }
            
            var itemHover = function($item){
                $item.parent().find(".suggest_item").removeClass("hover");
                $item.addClass("hover");
            }
            
            $.each(suggests, function(i, item){
            
                var $item_node = $suggest_cont.find(".prototype_node").clone().removeClass("prototype_node").css("display","block");
                var $parent_node = $suggest_cont.find(".prototype_node").parent();
                
                $item_node.html(item.suggest_label);
                
                $item_node.mouseover(function(){
                    itemHover($item_node);
                });
                
                $item_node.click(function(){
                    $node.val(item.name);
                    removeSuggest();
                    $node.focus();
                });
                
                $parent_node.append($item_node);
                
            });
            
            $node.unbind("keypress");
            $node.keypress(function(eventObject){
                
                $selected_item = $node.parent().find("div.suggest_cont ul li.hover");
                if ( $selected_item.length == 0 ) {
                    $selected_item = $node.parent().find("div.suggest_cont ul li:visible:eq(0)");
                    itemHover($selected_item);
                    return;
                }
                
                switch(eventObject.keyCode){
                    case 40:
                        itemHover($selected_item.next(".suggest_item"));
                        break;
                    case 38:
                        itemHover($selected_item.prev(".suggest_item"));
                        break
                    case 13:
                        itemHover($selected_item.prev(".suggest_item"));
                        if ($selected_item.length > 0) {
                            $selected_item.click();
                            return false;   
                        }
                        break
                }
            });
            
            $node.unbind("blur");
            $node.blur(function(){
                window.setTimeout(removeSuggest,200);
            });
                        
            $node.parent().after($suggest_cont);
            $suggest_cont.css("width",$node.outerWidth()).show();
            
        }';
        
        $this->js_presentation['suggest'] = 'function($node){
            var handler = this;
            var timeout;
            var last_str;
            
            $node.unbind();
            $node.keyup(function(eventObject){
                
                var $field = $(this);
                
                var getSuggestedList = function(str) {
                    if (!$.trim(str)) {
                        last_str = "";
                        handler.showSuggest($node, []);
                        return;
                    }   
                
                    var key = eventObject.which;
                    if ( last_str == str || key==13) {
                        return;
                    }
                    
                    var params = { str : str, action: "'.$this->responder_action.'" };
                    
                    $.ajax({
                        url: "'.URL_FIELD_RESPONDER.'",
                        method: "post",
                        dataType: "json",
                        data: params,
                        success: function(result){
                            last_str = str;
                            handler.showSuggest($node, result, eventObject);
                        }
                    });
                }
                
                var suggestGetList = function(){
                    var str = $field.val();
                    getSuggestedList(str);
                }
                
                if (timeout != undefined) {
                    window.clearTimeout(timeout)
                }
                timeout = window.setTimeout(suggestGetList,300);
                return false;
            });
        }';
        
    }

    public function validate( $value )
    {
        return $value;
    }
        
    public function render( array $params = null, SK_Form $form = null )
    {
        $value = $this->getValue();
                
        $output ='<div class="username_container"><table class="form"><tbody>';     
        $output.='<tr>
                    <td class="val">';
        $output.='<div><input type="text" autocomplete="off" name="'.$this->getName().'[user_name]" value="'.$value.'" /></div>';        
        $output.='</td></tr>';
        
        $output.= '</tbody></table>
            <div class="suggest_cont" style="display:none">
                <ul>
                    <li class="suggest_item prototype_node" style="display:none"></li>
                </ul>
            </div>
        </div>';
        
        return $output;
    }   
}