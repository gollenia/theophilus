document.getElementById("MediaApp")&&(MediaApp=new Vue({el:"#MediaApp",data:{callback:"",selection:[],id:id,imageList:[{url:"",id:"",title:""}]},computed:{slug:function(){return slugify(this.title)}},methods:{getImageList:function(){that=this,axios.post(ajaxUrl,{id:this.id,controller:"media",method:"list"}).then(function(t){console.log(t),that.imageList=t.data}).catch(function(t){console.log(t.response)})},saveImage:function(){uploadImage("File","music"),axios.post(ajaxUrl,{controller:"music",method:"saveCategory",data:{name:this.slug,title:this.title,image:document.getElementById("File").files[0].name}}).then(function(t){}).catch(function(t){})},openImageList:function(t){this.callback=t,this.getImageList(),UIkit.modal("#MediaApp").show()},returnImage:function(t){this.callback(t),UIkit.modal("#MediaApp").hide()}}}));