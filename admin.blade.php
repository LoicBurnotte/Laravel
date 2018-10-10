@extends('layout')

@section('content')
	<div class="row dark-section pab4 text-center">
		<div class="large-10 large-offset-1 end columns">
			<h2>{{trans('index.admin_kdoparty')}}</h2>
			<p>{{trans('index.admin_new_draw_bis')}}</p>
			<p><small>{{trans('index.admin_email_validate')}}<a href="javascript:document.location.reload(true)">{{trans('index.admin_refresh')}}</a></small></p>
		</div>
		<a href="{{route('admin-gift')}}"><button id="giftButton" class="button radius medium">{{trans('index.match_button')}}</button></a>
	</div>
	
	<div class="row bright-section pat4 text-center">
		<div class="large-10 large-offset-1 end columns">
			<?php $roleAdmin = $numberCircles; 
				  $roleUser = 0;
				  $circle_id = 0;	  
			 ?>
			@if ($numberCircles == 0)
				<h3>User : </h3>
			@else
				<h3>Admin : </h3>
			@endif
			<!-- <p id="user-id" value="{{$user->id}}">ID : {{$user->id}}</p> -->
			<p id="nameUser"><strong>{{$user->username}}</strong> <a href="javascript:void(0);" onclick="updateName();"><i class="fas fa-pen"></i></a></p>
			<div id="toggleNewName" class="hidden" >
				<input type="text" id="newName" style="width: 150px; margin: 0 auto;" placeholder="name"><br>
				<button id="saveNewName" class="button radius small">{{trans('index.create_save')}}</button>
			</div>
			<p><strong>{{$user->email}}</strong></p>
			<p>{{trans('index.admin_total_draws')}} : <i>{{$numberCircles}} {{trans('index.admin_draw')}}</i></p>
			
			<a href="{{route('admin-create')}}"><button class="button radius large">{{trans('index.admin_create_new_draw')}}</button></a>
			
			<ul class="tabs" data-tab role="tablist">
				<li class="tab-title active" role="presentation"><a href="#panel2-1" role="tab" tabindex="0" aria-selected="true" aria-controls="panel2-1">{{trans('index.admin_tirage')}} 1</a></li>
				@for ($i = 1; $i < $numberCircles; $i++)
					<li class="tab-title" role="presentation"><a href="#panel2-{{$i+1}}" role="tab" tabindex="0" aria-selected="false" aria-controls="panel2-{{$i+1}}">{{trans('index.admin_tirage')}} {{$i+1}}</a></li>
				@endfor
			</ul>
			<div class="tabs-content content-admin">
				<?php $count = 1; ?>				
				@foreach ($circles as $j => $circle)
					<!-- if($circle->admin_id == $user->id) -->
					@if ($j == 0)
						<section role="tabpane{{$j+1}}" aria-hidden="false" class="content active" id="panel2-{{$j+1}}">
					@else
						<section role="tabpane{{$j+1}}" aria-hidden="false" class="content" id="panel2-{{$j+1}}">
					@endif
							<!-- BUTTON DELETE CIRCLE-->
							<div id="circle{{$j}}" class="delete"><a href="javascript:void(0);" onclick="deleteCircle('{{$circle->id}}', {{$j}});">{{trans('index.delete_circle')}}</a></div>

							<h3 id="titleDraw">{{trans('index.admin_draw')}} N° {{$j+1}}</h3>
							<p>ID : {{$circle->id}}</p>
							<h3>{{trans('index.admin_reminder')}}</h3>
							<p><strong>{{$circle->title}}</strong></p>
							<p><i>{{$circle->group_name}}</i></p>
							<p><i>{{$circle->description}}</i></p>
							<h3>{{trans('index.admin_participants')}}</h3>
							
							<table class="table-admin">
							<thead>
								<tr>
									<th>#</th>
									<th>{{trans('index.admin_name')}}</th>
									<th>{{trans('index.admin_email')}}</th>
									<th>{{trans('index.admin_answer')}}</th>		
									<th>{{trans('index.admin_participate')}}</th>										
								</tr>
							</thead>
							<tbody>
							<?php $i=0; ?>
							@foreach ($persons as $person)
							<!-- 39 - 46 - 47 - 48 -->
								<!-- <p>{{$circle->id}} {{$person->circle_id}}</p> -->
								<!-- {{$circle->id}} -->
								@if ($circle->id == $person->circle_id)
								<?php $newCircle = $j; ?>
								<tr>
									<?php $validation = (null !== Input::old('num') && Input::old('num') == $i); ?>
									<td>{{$count}}</td>
									<td>{{$person->name}}</td>
									<td>
										<span class="toggleUpdateEmail" > {{$person->email}} </span>
										@if (!$person->is_invitation_email_received)
											<div class="toggleUpdateEmail" style="display:{{$validation ? 'auto' : 'none'}}">
												{{ Form::open(array('action' => 'PersonController@emailUpdate')) }}
													<div class="row collapse {{$validation && $errors->has('email') ? 'error' : ''}}">
														<div class="large-6 columns">
															{{Form::hidden('num', $i);}}
															{{Form::hidden('hash', $person->hash);}}
															{{Form::email('email', $value = ($validation ? Input::old('email') : $person->email), $attributes = array('class' => 'email'));}}
															@if ($validation && $errors->has('email'))
															<small class="error">{{ $errors->first('email') }}</small> 
															@endif
														</div>
														<div class="large-6 columns">
															{{Form::submit(trans('index.admin_save'), $attributes = array('class' => 'button postfix'))}}
														</div>
													</div>
												{{ Form::close() }}
											</div>
										@endif
									</td>							
									<td>
										<!-- $person->getInviteString() -->
										@if (!$person->is_invitation_email_received)
											<span class="toggleUpdateEmail" style="display:{{$validation ? 'none' : 'auto'}}">
												<br/>(<a class="updateEmail" href="javascript:void(0);">{{trans('index.admin_email_update')}}</a>)
											</span>
											<span  class="toggleUpdateEmail" style="display:{{$validation ? 'auto' : 'none'}}">
												<br/>(<a class="updateEmail" href="javascript:void(0);">{{trans('index.admin_cancel')}}</a>)
											</span>
										@endif
									</td>	
									<td>
										<div class="switch round">
											<?php 
											$options = array();
											$options['id']  = ('participation-'.$i);
											$options['data-hash']  = $person->hash;
											$options['class'] = 'participation';
											// if(!$person->is_invitation_email_received) {
											// 	array_push($options, 'disabled');
											// }
											?>
											{{Form::checkbox('participation-'.$i, null, $person->is_participate , $options)}}					 									  
											<label for="participation-{{$i}}"></label>
										</div>							
									</td>						
								</tr>
								@else
									<?php $newCircle = null; ?>	
								@endif
								@if ($newCircle != $j)
									<!-- <input type="number" value="{{$count}}" class="hidden" id="personsCount"> -->
									<?php $count = 0; ?>
								@endif
								<?php $count++; $i++;?>		
							@endforeach
							<tr>
								<td></td>
								<td colspan="3"><strong>{{trans('index.admin_total_participants')}}</strong></td>
								<td class="text-center"><span class="total-participants"></span></td>
							</tr>							
						</tbody>
					</table>
					
					<div class="large-10 large-offset-1 end columns text-center">
						<br>
						<a id="participation-supp-link" href="#participation-supp">{{trans('index.admin_last_minute')}}</a>
						<br>
						<div id="participation-supp" class="hidden">
							<div class="large-10 large-offset-1 end columns">
								{{ Form::open(array('action' => 'PersonController@addToCircle')) }}
									{{Form::hidden('circle_id', $circle->id);}}
									<div class="large-6 columns {{$errors->has('name_'.$i) ? 'error' : ''}}">
										<label>{{trans('index.admin_name')}}
											{{Form::text('name', $value = Input::old('name'), $attributes = array('class' => 'name'));}}
										</label>
									</div>
									<div class="large-6 columns {{$errors->has('email_'.$i) ? 'error' : ''}}">
										<label>{{trans('index.admin_email')}}
											{{Form::email('email', $value = Input::old('email'), $attributes = array('class' => 'email'));}}
										</label>
									</div>
									<div class="large-12 columns end">
										{{Form::submit('Ajouter', $attributes = array('class' => 'button radius small'))}}
									</div>			
								{{ Form::close() }}
								</div>
							</div>	
							<br>
							@foreach($rules as $r => $rule)
								@if($rule->circle_id == $circle->id)
								<div id="rule{{$r}}">
									<strong>{{$rule->name1}}</strong> {{trans('index.admin_not_match')}} <strong>{{$rule->name2}}</strong>
									<a href="javascript:void(0);" onclick="deleteRule('{{$rule->hash1}}', '{{$rule->hash2}}', {{$rule->circle_id}}, {{$r}});">X</a>
									<br>
								</div>
								@endif
							@endforeach
							<div id="ruleContent"></div>
							<br>
						<!-- BUTTON and FORM for adding RULES -->
							<button id="add-rules-button" class="button radius medium">{{trans('index.admin_new_rules')}}</button></a>
							<br>
								<div id="add-rules" class="hidden">
									<p>{{trans('index.admin_rule')}}<br>
									{{trans('index.admin_rule2')}} -2 </p>
									<div id="clone">
										<ul class="rules">
											<li>					
												<select name="select-person1" id="person1">
													@foreach ($persons as $x => $person)
														@if ($circle->id == $person->circle_id)
															<option name="name1" value="{{$person->name.'/'.$person->hash}}">{{$person->name}}</option>
														@endif
													@endforeach
												</select>															
											</li>
											<li>
												<h5>{{trans('index.admin_not_match')}}</h5>
												<h5>{{trans('index.admin_vice_versa')}}</h5>
											</li>
											<li>
												<select name="select-person2" id="person2">
													@foreach ($persons as $y => $person)
														@if ($circle->id == $person->circle_id)
															{{ $hash2 = $person->hash }}
															<option name="name2" value="{{$person->name.'/'.$person->hash}}">{{$person->name}}</option>											
														@endif
													@endforeach
												</select>
											</li>
											<li>
												<button class="button small" id="delete-rule" onclick="$(this).closest('ul').remove();">X</button>
											</li>
										</ul>
									</div>
									<button class="button small" id="new-rule">+</button>
									<br>
									<button type="submit" id="rules-button" class="button radius large">
										{{trans('index.admin_save')}}
									</button>
								</div>
							<hr>
						</div>
					<div class="large-10 large-offset-1 end columns">
						{{ Form::open(array('action' => 'CircleController@shuffle', 'id' => 'shuffle')) }}				
							{{Form::hidden('hash', $circle->hash);}}
							{{Form::submit(trans('index.admin_start_draw'), $attributes = array('class' => 'button radius large'))}}
						{{ Form::close() }}
					</div>
				</section>
			@endforeach
			</div>
		</div>
		
		<div class="large-10 large-offset-1 end columns">
			<p>{{trans('index.participants')}}</p>
			<!-- PART FOR NEW USERS who want to create wish list -->
			<table class="table-participants">
				<thead>
					<tr>
						<th>#</th>
						<th>{{trans('index.admin_draw')}} ID</th>
						<th>{{trans('index.admin_name')}}</th>
						<th>{{trans('index.admin_email')}}</th>									
					</tr>
				</thead>
				<tbody>
				<?php $userId = $user->id; ?>
				@foreach ($people as $x => $person)
					@if ($person->email == $user->email)
						<?php $cpt = 0; $circle_id = $person->circle_id; $personId = $person->id; $roleUser++; ?>
						@foreach ($people as $k => $person)
							@if ($circle_id == $person->circle_id)
								@if ($user->email == $person->email)
									<!-- <p>{{$person->id}} {{$roleUser}}</p> -->
									<input type="text" id="userId{{$roleUser}}" class="hidden" value="{{$person->id}}">
								@endif
								<?php $cpt++;?>
								<tr>
									<td>{{$cpt}}</td>
									<td>{{$circle_id}}</td>
									@if($person->email == $user->email)
										<td><strong>{{$person->name}}</strong></td>
										<td><strong>{{$person->email}}</strong></td>
									@else
										<td>{{$person->name}}</td>
										<td>{{$person->email}}</td>
									@endif
								</tr>
							@endif
						@endforeach
						<tr class="text-center">
							<td><input type="text" id="circleId{{$roleUser}}" class="data hidden" value="{{ $circle_id }}"></td>
							<td>
							@if($circles == null) 
								@foreach($wishCircles as $circle)
									<!-- if($circle->id == $circle_id) -->
										<strong>{{ $circle->title }}</strong><br>{{ $circle->description }}
									<!-- endif -->
								@endforeach
							@else
								@foreach($circles as $circle)
									@if($circle->id == $circle_id)
										<strong>{{ $circle->title }}</strong><br>{{ $circle->description }}
									@endif
								@endforeach
							@endif
							</td>
							<td style="margin: 0 auto; text-align: center;">
								<button id="add-wish-button-{{$roleUser}}" class="button radius medium">{{trans('index.wishlist_title')}}</button>
								<!-- <a href="#" data-reveal-id="myWishListModal{{$roleUser}}"><button id="add-wish-button" class="button radius medium">{{trans('index.wishlist_title')}}</button></a> -->
								<!-- WISHLIST  -->
								<!-- <div id="myWishListModal{{$roleUser}}" class="modalClass reveal-modal small text-center" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog"> -->
									<div class="wishlist hidden" id="wish-item-{{$roleUser}}">
										<h3>{{trans('index.wishlist_title')}}</h3>
										<p>{{trans('index.wishlist_content')}}</p>
										<div id="clone">
											<ul class="wish">
												<li>
													<input type="text" id="wishItem" placeholder="{{trans('index.wish_example')}}">
												</li>
												<li>
													<button class="button small" id="delete-rule" onclick="$(this).closest('ul').remove();">X</button>
												</li>
											</ul>
										</div>
										<button class="button small" id="new-wish">+</button>
										<br>
										<button type="submit" id="wishes-button" class="button radius large">
											{{trans('index.admin_save')}}
										</button>
									</div>
									<!-- <a class="close-reveal-modal" aria-label="Close">&#215;</a> -->
								<!-- </div> -->
							</td>
							<td>
								@foreach($wishlist as $i => $wish) <!-- $people[$x]->id -->
									<!-- <p>{{ $personId}} = {{$wish->user_id}}  +  {{$wish->circle_id}} == {{$circle_id}}</p> -->
									@if ($personId == $wish->user_id && $wish->circle_id == $circle_id)
									<div id="wish{{$i}}"><a href="javascript:void(0);" onclick="deleteWish('{{$wish->wish}}', {{$wish->user_id}}, {{$wish->circle_id}}, {{$i}});">X</a> {{$wish->wish}}<br></div>
									@endif
								@endforeach
								<div class="wishContent{{$roleUser}}"></div>
							</td>
						</tr>
					@endif
				@endforeach
				</tbody>
			</table>

			<p>{{trans('index.take_part_of')}} {{$roleUser}} {{trans('index.admin_draw')}}</p>
			<!-- BUTTON DELETE USER-->
			<div><a href="javascript:void(0);" onclick="deleteUser('{{$user->id}}');">{{trans('index.delete_user')}}</a></div>
        </div>
    </div>

	<!-- <div class="row bright-section pav4 text-center"></div> -->

@stop

@section('script')
	<script type="text/javascript">
	let total =  $(".content").length;
	for(let i = 1; i <= total; i++){
		let $parent = "#panel2-" + i;
		$(document).ready(function() {
			$($parent + " input.participation").change(function() {
				updateTotal();
				let hash = $(this).attr("data-hash");
				let value = $(this).prop("checked");
				$url = "admin/participation/" + hash + "/" + value;
				$.ajax($url)
					.done(function() {
						console.log( "success" );
					})
					.fail(function() {
						sweetAlert("Oops...", "{{trans('index.admin_msg0')}}", "error");
						console.log( "error " + $url );
					})
			});

			$($parent + " a.updateEmail").click(function() {
				$(this).closest("tr").find(".toggleUpdateEmail").toggle();
			});

			$($parent + " input.participation:disabled").parent().click(function() {
				sweetAlert("Oops...", "{{trans('index.admin_msg1')}}", "error");
			});

			$($parent + " form#shuffle").submit(function(){
				if (getParticipantsCount() < 3) {
					sweetAlert("Oops...", "{{trans('index.admin_msg2')}}", "error");				
				} else {
					$form = $(this);
					if (getParticipantsCount() < getTotalCount()) {
						msg = "{{trans('index.admin_msg3')}}" + getParticipantsCount() + "{{trans('index.admin_msg4')}}" + getTotalCount() + "{{trans('index.admin_msg5')}}";					
					} else {
						msg = "{{trans('index.admin_msg6')}}";
					}
					swal({
					title: "{{trans('index.admin_msg7')}}",
					text: msg,
					type: "{{trans('index.admin_msg8')}}",
					showCancelButton: true,
					confirmButtonText: "{{trans('index.admin_msg9')}}",
					cancelButtonText: "{{trans('index.admin_cancel')}}",
					},
					function(isConfirm) {
						if (isConfirm) {	
							$form.off("submit");
							$form.submit();
							$form.find($parent + " input[type=submit]").attr('disabled', true);
						}				  	
					});				
				}
				return false;
			});
			updateTotal();

			function getParticipantsCount() {
				return $($parent + " input.participation:checked").length;
			}
					
			function getTotalCount() {
				return $($parent + " input.participation").length;
			}

			function updateTotal() {		
				$($parent + " span.total-participants").text(getParticipantsCount());
			}

			// RULES RULES RULES RULES RULES RULES RULES RULES RULES
			$($parent + " a#participation-supp-link").on("click", function(){
				$($parent + " div#participation-supp").toggleClass('hidden');
			});
			
			$($parent + " button#add-rules-button").on("click", function(){
				$($parent + " div#add-rules").toggleClass("hidden");
			});

			$($parent + " #new-rule").on("click", function(){
				$($parent + " .rules").first().clone().appendTo($parent + " #clone");
			});

			// Method for adding RULES
			$($parent + " #rules-button").on("click", function(){
				let rulesNumber = $($parent + " .rules").length;
				let arrayUrl = [];
				for(let i = 1; i <= rulesNumber; i++){
					let parents = $($parent + " .rules:nth-child(" + i + ")");
					let name1 = $(parents).find("#person1 option:selected").val();
					let name2 = $(parents).find("#person2 option:selected").val();
					if(name1 != name2){
						// console.log(name1 + " " + name2 + "\n");
						let hash1 = name1.split("/");
						let hash2 = name2.split("/");
						$($parent + " #ruleContent").append("<strong>" + hash1[0] + "</strong> {{trans('index.admin_not_match')}} <strong>" + hash2[0] + "</strong><br>");
						// console.log(hash1[1] + " " + hash2[1] + "\n");
						$url = "admin/rules/" + hash1[1] + "/" + hash2[1];
						arrayUrl.push($url);
					}else{
						sweetAlert("Oops...", "{{trans('index.admin_alert_message')}}", "error");
					}
				}
				let numberUrl = arrayUrl.length;
				if(numberUrl > 0){
					for(let i = 0; i < numberUrl; i++){
						$.ajax(arrayUrl[i])
							.done(function() {
								console.log( "success " + arrayUrl[i]);
							})
							.fail(function() {
								sweetAlert("Oops...", "{{trans('index.admin_msg0')}}", "error");
								console.log( "error " + arrayUrl[i]);
							})
					}
					$($parent + " div#add-rules").toggleClass("hidden");
				}
			});
		});
	}
	// WISHLIST WISHLIST WISHLIST WISHLIST WISHLIST WISHLIST WISHLIST WISHLIST WISHLIST 
	$(document).ready(function(){
		let whishlistCount = $(".wishlist").length;
		let modals = $('#modalClass');		
		for(let j = 1; j <= whishlistCount; j++){
			let circleId = $("#circleId" + j).val();
			let userId = $("#userId" + j).val();
			// console.log("\nUserID = " + userId + "\nCircleID = " + circleId);
			let parent = "#wish-item-" + j;
			$("#add-wish-button-" + j).on("click", function(){
				$(parent).toggleClass("hidden");
			});
			
			$(parent + " #new-wish").on("click", function(){
				$(parent + " .wish").first().clone().appendTo(parent + " #clone");
				$(parent + " .wish").last().find("#wishItem").val("");
			});
			console.log($(parent + " .wish").length);

			// add WISH:
			$(parent + " #wishes-button").on("click", function(){
				let wishesCount = $(parent + " .wish").length;
				let arrayWishes = [];
				console.log(userId);
				for(let i = 1; i <= wishesCount; i++){
					let parents = $(parent + " .wish:nth-child(" + i + ")");
					let wish = parents.find("#wishItem").val();
					$('.wishContent' + j).append(wish + "<br>");
					wish = encodeURI(wish); 
					console.log(wish);
					$url = "admin/wish/" + wish + "/" + userId + "/" + circleId;
					arrayWishes.push($url);
				}
				let numberUrl = arrayWishes.length;
				if(numberUrl > 0){
					for(let i = 0; i < numberUrl; i++){
						// console.log(arrayWishes[i]);
						$.ajax(arrayWishes[i])
							.done(function() {									
								console.log("success " + arrayWishes[i]);
							})
							.fail(function() {
								// sweetAlert("Oops...", "{{trans('index.admin_msg0')}}", "error");
								console.log("error " + arrayWishes[i]);
							})
					}
					$(parent).toggleClass("hidden");
				}
			});
			
		}
	});
	// change Name
	function updateName(){
		$('#toggleNewName').toggleClass("hidden");
	}
	$("#saveNewName").on("click", function(){
		$newName = $("#newName").val();
		$("#nameUser").html("<strong>" + $newName + "</strong>")
		$newName = encodeURI($newName); 
		$url = "admin/update-user/" + $newName;
		$.ajax($url)
			.done(function() {									
				console.log("update : success " + $url);
				$("#toggleNewName").toggleClass("hidden");
			})
			.fail(function() {
				// sweetAlert("Oops...", "{{trans('index.admin_msg0')}}", "error");
				console.log("update : error " + $url);
			})
	});
	// newName = $("#newName").val();

	function deleteRule(hash1, hash2, circleId, index){
		$url = "/delete-rule/" + hash1 + "/" + hash2 + "/" + circleId;
		console.log($url);
		$.ajax($url)
			.done(function() {									
				console.log("delete : success " + $url);
				$("#rule" + index).hide();
			})
			.fail(function() {
				sweetAlert("Oops...", "{{trans('index.admin_msg0')}}", "error");
				console.log("delete : error " + $url);
			})
	}
	function deleteWish(wish, userId, circleId, index){
		wish = encodeURI(wish);
		$url = "/delete-wish/" + wish + "/" + userId + "/" + circleId;
		console.log($url);
		// windows.confirm("Voulez-vous vraiment supprimer ce souhait");
		$.ajax($url)
			.done(function() {									
				console.log("delete : success " + $url);
				$("#wish" + index).hide();
			})
			.fail(function() {
				sweetAlert("Oops...", "{{trans('index.admin_msg0')}}", "error");
				console.log("delete : error " + $url);
			})
	}
	function deleteCircle(circleId, index){
		console.log(circleId);
		$url = "/delete-circle/" + circleId;
		if(confirm("{{trans('index.delete_confirmation')}}")){
			$.ajax($url)
				.done(function() {									
					console.log("delete : success " + $url);
					$("#circle" + index).hide();
				})
				.fail(function() {
					sweetAlert("Oops...", "{{trans('index.admin_msg0')}}", "error");
					console.log("delete : error " + $url);
				})
		}
	}
	function deleteUser(userId){
		console.log(userId);
		$url = "/delete-user/" + userId;
		if(confirm("{{trans('index.delete_user_confirmation')}}")){
			$.ajax($url)
				.done(function() {									
					console.log("delete : success " + $url);
				})
				.fail(function() {
					sweetAlert("Oops...", "{{trans('index.admin_msg0')}}", "error");
					console.log("delete : error " + $url);
				})
		}
	}
	</script>
@stop
