<script>
    
    function remove_jcrop() {
       if (typeof "myjrop" == Jcrop) {
              myjrop.destroy();  
            } 
    }
    
    $(document).ready(function (){
            remove_jcrop();
            var myjrop = Jcrop.attach('jcrobtarget', {
                aspectRatio:4/3,
                
            });
            myjrop.listen('crop.update',(widget,e) => {
              var c = widget.pos;
              $('#jcrobx').val(c.x);
              $('#jcroby').val(c.y);
              $('#jcrobx2').val(c.x2);
              $('#jcroby2').val(c.y2);
              $('#jcrobw').val(c.w);
              $('#jcrobh').val(c.h); 
            });
            
            <% if ($img!="") %>
                
            <%/if%>
            
            var rect = Jcrop.Rect.create(10,10,400,300);
            var options = {};
            myjrop.newWidget(rect,options);

        
        $( "#js-ratio" ).change(function() {
            var ration = eval($(this).val());
            myjrop.setOptions({ aspectRatio: ration });
        });
        
        $("#js-ratio").append(new Option("4:3", "4/3"));
        $("#js-ratio").append(new Option("3:4", "3/4"));
        $("#js-ratio").append(new Option("2:3", "2/3"));
        $("#js-ratio").append(new Option("3:2", "3/2"));
        $("#js-ratio").append(new Option("16:9", "16/9"));
        $("#js-ratio").append(new Option("16:10", "16/10"));
        $("#js-ratio").append(new Option("1:1", "1/1"));
        $("#js-ratio").append(new Option("- none -", ""));
    
    });

</script>    