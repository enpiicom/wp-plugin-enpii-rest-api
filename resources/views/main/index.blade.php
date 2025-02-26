@extends('enpii-rest-api::layouts/wp-main')

@section('content')
	<div class="container">
		<h1><?php echo 'Enpii REST API Index page'; ?></h1>
		{{
			html()->div( esc_html( $message ) );
		}}
		<div class="message-content">Welcome to Enpii REST API</div>
	</div>
@endsection
