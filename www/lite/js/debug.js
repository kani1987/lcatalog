function Dump(d) {
    var s = '';
    for (var k in d)
    {
        s += "  "+k+"  ";
        if(d[k]=='undefined') s+=" ";
        else s+='::'+d[k];
    	s += "\n"
    }
    return s;
}

function MuchoDump(d,l) {
    if (l == null) l = 1;
    var s = '';
    if (typeof(d) == "object") {
        s += typeof(d) + " {\n";
        for (var k in d) {
            for (var i=0; i<l; i++) s += "  ";
            s += k+": " + MuchoDump(d[k],l+1);
        }
        for (var i=0; i<l-1; i++) s += "  ";
        s += "}\n"
    } else {
        s += "" + d + "\n";
    }
    return s;
}