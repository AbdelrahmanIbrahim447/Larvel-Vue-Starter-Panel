<template>
	    <div class="container">
        <div class="row" v-if="$gate.isAdmin()">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users Managment</h3>

                <div class="card-tools">
                 <!--  <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                    <div class="input-group-append">
                      <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                  </div> -->
                  <div class="pt-20">
                  	
	                  <button class="btn btn-info" @click="openform()">
	                  	<i class="fas fa-user-plus"></i> Add New
	                  </button>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Registerd At</th>
                      <th>Modify</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                      <td>{{user.id}}</td>
                      <td>{{user.name}}</td>
                      <td>{{user.email}}</td>
                      <td class="text-center"><span class=" badge" :class="{'badge-success' :user.type == 'admin','badge-info':user.type == 'author',' badge-primary': user.type == 'user'}">{{user.type | upperCase}}</span></td>
                      <td>{{user.created_at | FormatDate}}</td>
                      <td>
                      	<a href="" class="btn btn-success btn-sm" @click.prevent="openform(user)">
                      		<i class="fa fa-edit"></i> Edit
                      	</a>
                      	<a href="" @click.prevent="deleteUser(user.id)" class="btn btn-danger btn-sm">
                      		<i class="fa fa-trash"></i> Delete
                      	</a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="users" show-disabled @pagination-change-page="getResults">
                  <span slot="prev-nav">&lt; Previous</span>
                  <span slot="next-nav">Next &gt;</span>
                </pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- Modal -->
		<div class="modal fade" id="Adduser" tabindex="-1" role="dialog" aria-labelledby="AdduserLabel" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" v-show="!editMode" id="AdduserLabel">Create User</h5>
            <h5 class="modal-title" v-show="editMode" id="AdduserLabel">Edit User</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
        <form @submit.prevent="editMode ? editUser() :createUser()" @keydown="form.onKeydown($event)">
		      <div class="modal-body">
              <div class="form-group">
                <label>Username</label>
                <input v-model="form.name" type="text" name="name"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                <has-error :form="form" field="name"></has-error>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input v-model="form.email" type="text" name="email"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                <has-error :form="form" field="email"></has-error>
              </div>
              <div class="form-group">
                <label>Bio</label>
                <input v-model="form.bio" type="text" name="bio"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                <has-error :form="form" field="bio"></has-error>
              </div>
              <div class="form-group">
                <label>Type</label>
                <select name="type" id="" v-model="form.type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }" >
                  <option value="admin">Admin</option>
                  <option value="user" selected="selected">Standard User</option>
                  <option value="author">Author</option>
                </select>
                <has-error :form="form" field="type"></has-error>
              </div>
              <div class="form-group">
                <label>Password</label>
                <input v-model="form.password" type="password" name="password"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                <has-error :form="form" field="password"></has-error>
              </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        <button type="submit" v-show="!editMode" class="btn btn-primary"><i class="fas fa-user"></i> Create</button>
            <button type="submit" v-show="editMode" class="btn btn-primary"><i class="fas fa-user"></i> Update</button>
		      </div>
        </form>
		    </div>
		  </div>
		</div>
    </div>
</template>

<script>
  import {eventBus} from '../app';
	export default {

    data(){
      return {
        editMode: true , // form mode wether edit/create
        form : new Form({
            id    :'',
            name  :'',
            email :'',
            password :'',
            type :'',
            bio :'',
            photo :'',
        }),
        users : {},
        }
      },
        methods:{
              // Our method to GET results from a Laravel endpoint
          getResults(page = 1) {
              axios.get('api/user?page=' + page)
                .then(response => {
                  this.users = response.data;
                });
          },
          openform(user = null){
            if (user != null) { // editing mode is on
               this.editMode = true; 
               this.form.reset();
               $('#Adduser').modal('show');
               this.form.fill(user);
            }else{ // creating user is on
              this.editMode = false;
               this.form.reset();
               $('#Adduser').modal('show');
            }
          },
          loadUsers(){
            if (this.$gate.isAdmin()) {
              // load users from database
              axios.get("api/user").then(({ data }) => (this.users = data));
            }
          },
          deleteUser(id){
            swal.fire({
                // confirming form
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
                if (result.value) {
                    this.form.delete('api/user/' + id).then(()=>{
                      // deleted alert succesful
                      swal.fire(
                        'Deleted!',
                        'User has been deleted.',
                        'success'
                      )
                      // reload users
                      this.loadUsers();
                    }).catch(()=>{
                      // error alert
                      swal.fire({
                          type: 'error',
                          title: 'Oops...',
                          text: 'Something went wrong!',
                        })
                    });
                }
              })
          },
          createUser(){
            // start progress bar
            this.$Progress.start()
            // submit form with data to create user
            this.form.post('api/user')
            .then((data)=>{
              // show success message 
              toast.fire({
                  type: 'success',
                  title: 'User added successfully'
               })
              // hide add modal 
              $('#Adduser').modal('hide');
              // hide success message
              $('.alert').fadeOut(8000);
              // end progress bar
              this.$Progress.finish();
              // refresh the usere table after adding data 
              this.loadUsers();              

            }).catch((error)=>{
              // failed progress bar
              this.$Progress.fail();  
            });
          },
          editUser(){
            this.$Progress.start()
            this.form.put('api/user/' + this.form.id)
            .then((data)=>{
              // show success message
                  toast.fire({
                    type: 'success',
                    title: 'User edited successfully'
                 });
                // hide edit modal 
                $('#Adduser').modal('hide'); 
                // end progress bar
                this.$Progress.finish();
                // refresh the usere table after adding data 
                this.loadUsers();             
            })
            .catch(()=>{
               // failed progress bar
                this.$Progress.fail();

               // failed error message
               swal.fire({
                  type: 'error',
                  title: 'Oops...',
                  text: 'Something went wrong!',
                })
             })
          }
        },
        created(){
          eventBus.$on('searching',()=>{
            this.$Progress.start()
            // get search data from input
            let query = this.$parent.search;
            if (query.length > 0) { 
              // make a search query to server and get results
              axios.get('api/search/user/' + query)
              .then((data)=>{
                // end progress bar
                this.$Progress.finish();
                this.users = data.data

              })
              .catch(()=>{
                // failed progress bar
                this.$Progress.fail();
              })
            }else{
              // end progress bar
              this.$Progress.finish();
            }
          });
          // load users in datatable
          this.loadUsers();
        }
      }
		
</script>