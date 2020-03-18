<template>
    <div>
        
	    <div class="container">
            <div class="col-md-12">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header text-white" style="background: url('./background.jpg') center center;">
                <h3 class="widget-user-username text-left">{{this.form.name}}</h3>
                <h5 class="widget-user-desc text-left">{{this.form.type}}</h5>
              </div>
              <div class="widget-user-image">
                <img class="img-c Avatar" :src="showProfileImage()">
              </div>
              <div class="card-footer">
                <div class="row">
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">3,200</h5>
                      <span class="description-text">SALES</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">13,000</h5>
                      <span class="description-text">FOLLOWERS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header">35</h5>
                      <span class="description-text">PRODUCTS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
            </div>
        </div>
        <!-- /.widget-user -->
            <div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link " href="#activity" data-toggle="tab">Activity</a></li>
                  <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class=" tab-pane" id="activity">
                   

                    </div>
                  <div class="tab-pane active" id="settings">
                    <form class="form-horizontal"  enctype="multipart/form-data">
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
                <label>Profile Image</label>
                <input  type="file" name="photo" @change="updateImage" class="form-control" :class="{ 'is-invalid': form.errors.has('photo') }">
                <has-error :form="form" field="photo"></has-error>
              </div>
              <div class="form-group">
                <label>Password</label>
                <input v-model="form.password" type="password" name="password"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                <has-error :form="form" field="password"></has-error>
              </div>
                      <div class="form-group row">
                        <div class="col-sm-10">
                          <button type="submit" @click.prevent="updateProfile" class="btn btn-success">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          </div>
    </div>
</template>

<script>
    export default {
        data(){
          return {
                form : new Form({
                    name  :'',
                    email :'',
                    password :'',
                    type :'',
                    bio :'',
                    photo :'',
                }),
                tempImage:null,
            }
          },
        methods :{
            showProfileImage(){
                return '/images/'+ this.tempImage;
            },
            updateImage(e){
                // get image data from input
                let file = e.target.files[0];
                // make a reader instance
                var reader = new FileReader();
                //assign file to form.photo
                reader.onloadend = (file) =>{
                    this.form.photo = reader.result;
                }
                reader.readAsDataURL(file);
            },
            updateProfile(){
                // start progress bar
                this.$Progress.start()
                // send update profile request
                this.form.put('api/profile/update')
                .then((data)=>{
                     // show success message
                        toast.fire({
                        type: 'success',
                        title: 'Profile updated successfully'
                     });
                     // end progress bar
                    this.$Progress.finish();
                    // reassign image name to form
                    this.tempImage = data.data; 
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
            // load user data from database
            axios.get("api/profile").then((data)=>{
                this.form.fill(data.data);
                // assing image to tmpimage 
                this.tempImage = this.form.photo;
            });     
        }
    }
</script>