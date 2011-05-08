<?php
include('sgs.php');
$file = new sgs();
?>
<script type="text/javascript">
parent_document = (window.parent.document);
var xx =parent_document.getElementById('pic_path');
xx.innerHTML = '<?php echo $file->destination;?>'; 
var divDoms = parent_document.getElementsByTagName('div');
for(var i=0; i < divDoms.length;i++){
    if(divDoms[i]){
       var classString = (divDoms[i].getAttribute('class')||divDoms[i].getAttribute('className'));
    }
    if(classString && classString.indexOf('loading')>=0){
       var loadingDom = divDoms[i]; 
    }
}

loadingDom.setAttribute('class', 'loading hide');
loadingDom.setAttribute('className', 'loading hide');
var preview = parent_document.getElementById('pic_preview');
preview.setAttribute('class', 'preview');
preview.setAttribute('className', 'preview');
preview.setAttribute('src', '../<?php echo $file->destination;?>');
console.log(1);
</script>
