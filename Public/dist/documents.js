
Vue.component('item', {
	  template: '#item-template',
	  props: {
	    model: Object
	  },
	  data: function () {
	    return {
	      open: false
	    };
	  },
	  computed: {
	    isFolder: function () {
	      return this.model.children &&
	        this.model.children.length;
	    }
	  },
	  methods: {
	    toggle: function () {
	      if (this.isFolder) {
	        this.open = !this.open;
	      }
	    },
	    changeType: function () {
	      if (!this.isFolder) {
	        Vue.set(this.model, 'children', []);
	        this.addChild();
	        this.open = true;
	      }
	    },
	    addChild: function () {
	      this.model.children.push({
	        name: 'new stuff'
	      });
	    }
	  }
	});

function init() {
	if (localStorage.getItem('sidebar') === "true" && document.getElementById('sidebar')) {
		document.getElementById('main').classList.add("sidebar-active");
	}
	setTimeout(function(){ 
		document.getElementById('main').classList.add("ready");
	}, 200);
	
}

function toggleSidebar() {
	var main = document.getElementById('main');
	if (main.classList.contains('sidebar-active')) {
		main.classList.remove("sidebar-active");
		localStorage.setItem('sidebar', false);
		
	} else {
		main.classList.add("sidebar-active");
		localStorage.setItem('sidebar', true);
	}
}

window.onload = function() {
	init();
};



if(document.getElementById("SidebarApp")) {
	

// boot up the demo
	TreeApp = new Vue({
		el: '#SidebarApp',
		data: {
    		treeData: sidebarTree
		}
	});
}




if (document.getElementById("EditApp")) {
	Vue.use(window.VueCodemirror);
	Vue.use(window.VueShortkey);
	EditApp = new Vue({
		el: '#EditApp',
		data: { 
			page: pageData,
			cmOptions: {
		        tabSize: 4,
		        mode: 'text/javascript',
		        theme: 'base16-dark',
		        lineNumbers: false,
		        line: false,
		        lineWrapping: true
		    }
		},
		methods: {
			onCmReady(cm) {
		      
		    },
		    onCmFocus(cm) {
		      
		    },
		    onCmCodeChange(newCode) {
		      this.page.content = newCode
		      document.getElementById('save').classList.add("unsaved");
		    },
		    
		    
		    textWrap: function(before, after) {
		    	var selection = this.$refs.txCodeEdit.codemirror.getSelection();
		    	this.$refs.txCodeEdit.codemirror.replaceSelection(before + selection + after);
		    	var position = this.$refs.txCodeEdit.codemirror.getCursor();
		    	position.ch = position.ch - after.length;
		    	this.$refs.txCodeEdit.codemirror.setCursor(position);
		    	this.$refs.txCodeEdit.codemirror.focus()
		    },
		    
		    insertImage: function(id) {
		    	this.$refs.txCodeEdit.codemirror.replaceSelection("{{" + id + "}}");
		    },
		    
			save: function(andReturn) {
			    axios.post(ajaxUrl, {
	                controller: "page",
	                method: "save",
	                text: this.page.content,
	                id: this.page.id
	            }).then(function(response) {
	                console.log(response);
	                document.getElementById('save').classList.remove("unsaved");
	            }).catch(function(error){
	                console.log(error);
	            });
			}	
		}
	});
}