@extends('adminPopup')
@section('content')
	<div class="popup-title">
        <h1>{{ trans("admin/massEmail.composer.mass_mail_variables")}}</h1>
    </div>
    <div class="popup-form">
        {{ Form::open(array('id'=>'selVariable', 'method'=>'post','class' => 'form-horizontal border-type1' )) }}
        	<table class="table table-striped table-bordered table-hover">
            @foreach($details as $key => $val)
				<tr>
                	<td><a href="javascript:void(0);" onClick="javascript:callVariableEntry('{{ $key }}');">{{ $key }}</a></td>
                    <td>{{ $val }}</td>
                </tr>
             @endforeach
            </table>
        {{Form::close() }}
    </div>
@stop
@section('includescripts')
<script type="text/javascript">
	function callVariableEntry(myValue) {
		if(parent.tinyMCE!=undefined){
			parent.tinyMCE.activeEditor.execCommand('mceInsertContent', false, myValue);
			//add an empty span with a unique id
			/*var endId = parent.tinymce.DOM.uniqueId();
			ed.dom.add(ed.getBody(), 'span', {'id': endId}, '');
			//select that span
			var newNode = ed.dom.select('span#' + endId);
			ed.selection.select(newNode[0]);
			newNode[0].innerHTML = myValue; */
			/*
			var ed = parent.tinyMCE.get('content');
			var range = ed.selection.getRng();
			var newNode = ed.getDoc().createElement("p");
			newNode.innerHTML = myValue;
			range.insertNode(newNode);
			*/

		} else {
			if (opener.document.selection) {
			myField.focus();
			sel = opener.document.selection.createRange();
			sel.text = myValue;
			}
			else if (myField.selectionStart || myField.selectionStart == 0) {
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos,myField.value.length);
			}else{
			myField.value += myValue;
			}
		}
		parent.$.fancybox.close();
	}
</script>
@stop