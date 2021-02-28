require.config({baseUrl: "/templates/base/Admin/js/"});
$(function() {
	require(['../script'], function() {
		require.config({baseUrl: "/templates/project/Admin/js/"});
			
	});
});