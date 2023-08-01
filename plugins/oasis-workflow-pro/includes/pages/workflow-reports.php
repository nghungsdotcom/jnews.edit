<?php
/*
 * Workflow Reports Main Page
 *
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$selected_tab = ( isset ( $_GET['tab'] ) && sanitize_text_field( $_GET["tab"] ) ) ? sanitize_text_field( $_GET['tab'] )
	: 'userAssignments';
?>
<div class="wrap">
	<?php
	$report_tabs = array(
		'userAssignments'     => esc_html__( 'Current Assignments', "oasisworkflow" ),
		'workflowSubmissions' => esc_html__( 'Workflow Submissions', "oasisworkflow" ),
		'taskByDueDate'       => esc_html__( 'Assignments By Due Date', "oasisworkflow" )
	);
	$nonce = wp_create_nonce( 'ow_report_nonce' );
	echo '<div id="icon-themes" class="icon32"><br></div>';
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $report_tabs as $report_tab => $name ) {
		$class = ( $report_tab == $selected_tab ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab" . esc_attr( $class ) . "' href='?page=oasiswf-reports&tab=" . esc_attr( $report_tab ) .
		     "&_wpnonce=" . esc_attr( $nonce ) . "'>" .
		     esc_html( $name ) .
		     "</a>";

	}
	echo '</h2>';
	switch ( $selected_tab ) {
		case 'userAssignments' :
			include( OASISWF_PATH . "includes/pages/workflow-assignment-report.php" );
			break;
		case 'workflowSubmissions' :
			include( OASISWF_PATH . "includes/pages/workflow-submission-report.php" );
			break;
		case 'taskByDueDate' :
			include( OASISWF_PATH . "includes/pages/workflow-by-due-date-report.php" );
			break;
	}
	?>
</div>