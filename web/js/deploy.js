$(function(){
	new Deploy(deployParams);
	setupProjectSelect();
});

function setupProjectSelect() {
	var project = $("#project-current").val();
	
	$("#project-selection option").each(function(index) {
		var option = $(this).text();
		if(  option.trim() == project ) {
			$(this).prop('selected', true);
		}
	});
}

function Env() {
	this.value = "";
	this.isSet = false;
};

function Deploy(params) {
	
	this.project = params.project;
	this.urls 	 = params.urls;
	this.envs 	 = params.envs;
	this.labels	 = params.labels;
	this.load();
	this.initEvents();
	
	this.prod 		= new Env();
	this.preprod 	= new Env();
	this.qualif 	= new Env();
}

Deploy.prototype = {
		
		load : function() {
			
			// all branches
			this.getAllBranchs();
			// existing tags
			this.getAllTags();
			// branche de qualif
			this.getBranch(this.envs.QUALIF.id);
			// branche de preprod
			this.getBranch(this.envs.PREPROD.id);
			// Tag prod
			this.getBranch(this.envs.PROD.id);
		},
		
		initEvents: function(){
			
			$("#project-selection").change(function() {
				 window.location.href = $(this).val();
			});
			
			// CHANGE SOURCE
			$("#activ-qualif").on("click", $.proxy(function(e) {
				this.onChangeSource(e, this.envs.QUALIF.id);
				$("#activ-qualif").addClass("btn-red");
				$("#currentQualif").text(this.labels.loading);
				
			}, this));
			
			$("#activ-preprod").on("click", $.proxy(function(e) {
				this.onChangeSource(e, this.envs.PREPROD.id);
				$("#activ-preprod").addClass("btn-red");
				$("#currentPreProd").text(this.labels.loading);
			}, this));
			
			$("#activ-prod").on("click", $.proxy(function(e) {
				this.onChangeSource(e, this.envs.PROD.id);
				$("#activ-prod").addClass("btn-red");
				$("#currentProd").text(this.labels.loading);
			}, this));
			
			// MISE A JOUR SOURCE
			$("#update-qualif").on("click", $.proxy(function(e) {
				$("#update-qualif").removeClass("btn-green");
				$("#update-qualif").addClass("btn-red");
				
				this.onUpdateSource(e, this.envs.QUALIF.id);
			}, this));
			
			$("#update-preprod").on("click", $.proxy(function(e) {
				$("#update-preprod").removeClass("btn-green");
				$("#update-preprod").addClass("btn-red");
				
				this.onUpdateSource(e, this.envs.PREPROD.id);
			}, this));
			
			$("#update-prod").on("click", $.proxy(function(e) {
				$("#update-prod").removeClass("btn-green");
				$("#update-prod").addClass("btn-red");
				
				this.onUpdateSource(e, this.envs.PROD.id);
			}, this));
			
			// NEW TAG
			$("#new-tag").on("click", $.proxy(function(e) {
				this.onCreateTag(e, this.envs.PREPROD.id);
				$("#new-tag").addClass("btn-red");
			}, this));
			
			$("#update-info").on("click", $.proxy(function(e) {
				this.onFetchData(e, this.envs.QUALIF.id);
				$("#update-info").addClass("btn-red");
			}, this));
			
			$("#show-prod").click(function() {
				$("#content").toggle(500);
			});
		},
		
		getBranch: function(id){
			
			var whoToDisplay;
			var selectReturn;
			
			switch (id) {
				case  this.envs.QUALIF.id :
					whoToDisplay = $("#currentQualif");
					selectReturn = "#qualif";
					break;
					
				case this.envs.PREPROD.id :
					whoToDisplay = $("#currentPreProd");
					selectReturn = "#preprod";
					break;
					
				case this.envs.PROD.id :
					whoToDisplay = $("#currentProd");
					selectReturn = "#prod";
					break;
			};
			
			// vide le texte courant
			whoToDisplay.text(this.labels.loading);
			
			var callback = $.proxy(function(response) {
				
					if(response.status == "success"){
						
						switch (id) {
						case  this.envs.QUALIF.id :
							whoToDisplay = $("#currentQualif");
							this.qualif.value = response.data.branch.trim();
							
							break;
							
						case this.envs.PREPROD.id :
							whoToDisplay = $("#currentPreProd");
							this.preprod.value = response.data.branch.trim();
							
							break;
							
						case this.envs.PROD.id :
							whoToDisplay = $("#currentProd");
							
							if (this.prod.value == "") {
								this.prod.value = response.data.branch.trim();
							}
							break;
					};
										
					whoToDisplay.text(response.data.branch);
					this.placeAllSelect(whoToDisplay);
				}
			}, this);
			
			var url = this.urls.branch+"/"+id;
			// appel ajax
			this.actionAjax(url, false, callback);

		},
		
		getTag: function(id){
			
			// vide le champ
			$("#currentProd").text(this.labels.loading);
			
			var callback = $.proxy(function(response) {
				if(response.status == "success"){
					
					if (this.prod.value == "") {
						this.prod.value = response.data.branch;
						$("#currentProd").text(this.prod);
					}
				}
			}, this);
			
			var url = this.urls.tag+"/"+id;
			// appel ajax
			this.actionAjax(url, false, callback);
		},
		
		getAllBranchs: function(){
			
			$("#all-branches").html(this.labels.loading);
			
			var callback = $.proxy(function(response) {
				if(response.status == "success"){
					var html = "";
					var options = ""; // "<option>[select]</option>";
					for( index in response.data) {
						var val =response.data[index];
						html += "<tr><td>"+val+"</td></tr>";
						options += "<option value='"+val.trim()+"'>"+val.trim()+"</option>";
					}
					$("#all-branches").html(html);
					$(".select-branch").each(function() {
						$(this).html(options);
					});
					
					this.placeAllSelect();
				}
			}, this);
			
			var url = this.urls.allB+"/"+this.project;
			// appel ajax
			this.actionAjax(url, false, callback);
			
		},
		
		getAllTags: function(){
			
			var callback = $.proxy(function(response) {
				if(response.status == "success"){
					
					var options = "";
					for( index in response.data) {
						var val =response.data[index];
						options += "<option value='"+val+"'>"+val+"</option>";
					}
					
					$(".select-tag").each(function() {
						$(this).html(options);
					});
				}
			}, this);
			
			var url = this.urls.allT+"/"+this.project;
			// appel ajax
			this.actionAjax(url, false, callback);
			
		},
		
		onUpdateSource :  function(e, id) {
			e.preventDefault();
			
			// loading
			this.disableButton();
			
			var callback = function() {
				$("button[id*='update-']").each(function(){
					$(this).addClass("btn-green");
				});
			};
			
			var url = this.urls.update+"/"+id;
			// appel ajax
			this.actionAjax(url,true, callback);
		
		},
		
		onChangeSource : function(e, id)  {
			e.preventDefault();
			
			// loading
			this.disableButton();

			var source = "";
			
			switch(id) {
				case this.envs.QUALIF.id :
					source = $("#qualif select option:selected").text();
					break;
				case this.envs.PREPROD.id :
					source = $("#preprod select option:selected").text();
					break;
				case this.envs.PROD.id :
					source = $("#prod select option:selected").text();
					break;
				default:
					alert("source not found");
			}
			
			var url = this.urls.changing+"/"+id+"/"+source.trim();
			var callback = $.proxy(function() {
				if (id == this.envs.PROD.id ) {
					this.getTag(id);
				}
				else {
					this.getBranch(id);
				}
			}, this);
			
			// appel Ajax
			this.actionAjax(url, true, callback);
			
		},
		
		onCreateTag : function(e, id)  {
			e.preventDefault();
			
			// loading
			this.disableButton();
			
			var tag = $("#input-new-tag").val();
			
			if (tag.trim() == "") {
				$("#input-new-tag").addClass("input-error");
			}
			else {
				$("#input-new-tag").removeClass("input-error");
				
				var url = this.urls.create+"/"+id+"/"+tag;
				
				// callback après ajax
				var callback = $.proxy(function() {
					this.getAllTags();
				}, this);
				
				// appel Ajax
				this.actionAjax(url, true, callback);
				
			}
			
		},
		
		onFetchData : function (e, id) {
			e.preventDefault();
			
			// loading
			this.disableButton();
			
			// callback après ajax
			var callback = $.proxy(function() {
				this.getAllBranchs();
			}, this);
			
			var url = this.urls.fetch+"/"+id;
			// appel Ajax
			this.actionAjax(url, true, callback);
		},
		
		disableButton : function() {
			// unable les boutons
			$("button").each(function() {
				$(this).addClass("btn-disabled");
			});
		},
		
		enableButton : function() {
			$("button").each(function() {
				$(this).removeClass("btn-disabled");
				$(this).removeClass("btn-red");
			});
		},
		
		jsonToHtml : function(response, isOk) {
			var html = isOk 
				? "<div style='color:green'>"
				: "<div style='color:red'>";
			
			// tableau de réponse
			if (response.constructor === Array){
				for( index in response) {
					var val = response[index];
					html += "<div>"+ val +"</div>";
				}
			}
			// reponse unique
			else {
				html += "<div>"+ response +"</div>";
			}
			
			html += "</div>";
			return html;
		},
		
		actionAjax : function(url, showLog, callback) {
			// valeur par default
			var wantLog = typeof showLog !== 'undefined' 
						? showLog 
						: true;
			
			$.ajax({
				url: url,
				method: "GET",
				dataType: "json",
				cache: false
			}).success($.proxy(function(response) {	
								
				if(response.status == "success"){
					if (wantLog) {
						var html = this.jsonToHtml(response.data, true);
						$("#bash-return").html(html);
					}					
				}
				else {
					var html = this.jsonToHtml(response.message, false);
					$("#bash-return").append(html);
				}
				
				this.enableButton();

				if (callback !== undefined) {
					callback(response);
				}
				
			}, this)).error(function(jqXHR, textStatus, errorThrown){
				
				$("#bash-return").html(jqXHR.responseText);
				this.enableButton();

			});
			
		},
		
		placeAllSelect : function(who) {
			
			var options;
			var value = "";
			
			if (this.qualif.value != "" && !this.qualif.isSet) {
				
				var qualif = this.qualif.value;
				var select = $("#qualif select");
				
				this.placeSelect(select, qualif);
				this.qualif.isSet = true;
			}
			
			if (this.preprod.value != "" && !this.preprod.isSet) {
				
				var preprod = this.preprod.value;
				var select = $("#preprod select");
				
				this.placeSelect(select, preprod);
				this.preprod.isSet = true;
			}
			
			if (this.prod.value != "" && !this.prod.isSet) {
				
				var prod = this.prod.value;
				var select = $("#prod .select-branch");
				
				this.placeSelect(select, prod);
				this.prod.isSet = true;
			}
			
		},
		
		placeSelect : function(select, value) {
			
			select.find("option").each(function(index) {
				var option = $(this).text();
				if ( option.trim() == value ) {
					$(this).prop('selected', true);
				}
			});
			
		}
}