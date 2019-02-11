function find_object(n, d) {
  var p,i,x;  if(!d) d=document;
  if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);
  }
  if(!(x=d[n])&&d.all) x=d.all[n];
  for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=find_object(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n);
  return x;
}

function show_hide_layers() {
  var i,p,v,obj,args=show_hide_layers.arguments;
  for (i=0; i<(args.length-2); i+=3)
    if ((obj=find_object(args[i]))!=null) {
	  v=args[i+2];
      if (obj.style) { obj=obj.style; v=(v=='show')?'block':(v=='hide')?'none':v; }
      obj.display=v;
	}
}

function focus_user() {
	document.login.username.focus();
}
