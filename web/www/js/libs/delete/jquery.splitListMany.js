jQuery.fn.extend({
    splitListMany: function(cols){
        var list = $(this);
        var listLen = $(this).length;
        var colSize;
        var columns;

        if ((cols == null)||(cols <= 0)||(columns >= listLen)) { columns = 2; }
        else if (cols >= (listLen/2)) { columns = Math.floor(listLen/2); }
        else { columns = cols; }

        if (listLen%columns > 0) { colSize = Math.ceil(listLen/columns); }
        else { colSize = listLen/columns; }

        for(var i=1; i <= columns; i++){
            list.slice((i-1)*colSize,i*colSize).wrapAll('<ul class="lists list-'+i+'">');
        }
        return $(this);
    }
});