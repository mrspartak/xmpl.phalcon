$(function(){
	
})

Chart = {
	vars : {
		charts : {
			chart1 : 'chart1'
		}
	},
	
	init : function(){
		$.getJSON( '/poll/ajax', {type: 'genderAge'}, Chart.functions.prepare ); 
		$.getJSON( '/poll/ajax', {type: 'modern'}, Chart.functions.prepare );
		$.getJSON( '/poll/ajax', {type: 'genres'}, Chart.functions.prepare );
	},
	
	functions : {
		prepare : function( data ) {
			arr = []
			$.each( data.data, function( i, val ){
				ar = [ val.row, {label: val.label } ]
				arr.push( ar )
			} )
			chart = 'chart' + data.chart
			
			time = new Date( data.timestamp*1000  )
			t = time.getDate() +'/'+ (time.getMonth()+1) +'/'+ time.getFullYear() +' '+ time.getHours() +':'+ time.getMinutes() +':'+ time.getSeconds()
			$('.chart_title_' + data.chart).find('.label').text( 'обновление ' + t );
			
			Chart.functions.drawChart( chart, arr, data.opt)
		},
		
		drawChart : function( obj, data, opt ){
			variants = [{
				barLabel:  function(index) {
					amount = ($(this[0]).sum() * 100).toFixed(0);
					return $.tufteBar.formatNumber(amount) + '%';
				},
				legend: {
					data: ["мужчины", "женщины"],
					color: function(index) { 
						return ['#E57536', '#82293B'][index % 2] 
					},
				},
				color1:     function(index) { 
				  return ['#E57536', '#82293B'][index % 2] 
				},
				color2:     function(index, stackedIndex) { 
				  return ['#E57536', '#82293B'][stackedIndex % 2] 
				}	
			},{
				barLabel:  function(index) {
					amount = ($(this[0]).sum() * 100).toFixed(0);
					return $.tufteBar.formatNumber(amount) + '%';
				},
				color1:     function(index) { 
				  return ['#E57536', '#82293B'][index % 2] 
				},
				color2:     function(index, stackedIndex) { 
				  return ['#E57536', '#82293B'][stackedIndex % 2] 
				}
			}]
			
			$('#' + obj).tufteBar({
				data: data,
				barLabel:  variants[opt].barLabel,
				axisLabel: function(index) { return this[1].label },
				legend: variants[opt].legend,
				color: variants[opt].color1,
				color: variants[opt].color2
			});
		}	
	}
}