<template>
    <div>
        <nav class="navbar navbar-expand-md fixed-top navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Document Tracker</a>
            <button
                class="navbar-toggler p-0 border-0"
                type="button"
                data-toggle="collapse"
                data-target="#navbarsExampleDefault"
                aria-controls="navbarsExampleDefault"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-collapse collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <router-link to="/all-documents" class="nav-link">Documents</router-link>
                    </li>
                    <!-- <li class="nav-item">
                    <router-link to="/view-documents" class="nav-link">My Documents</router-link>
                    </li>-->
                    <li class="nav-item">
                        <router-link to="/receive" class="nav-link">Receive</router-link>
                        <!-- <a href="#"  @click="openReceive = true" class="nav-link">Receive</a> -->
                    </li>
                    <li class="nav-item">
                        <router-link to="/release" class="nav-link">Release</router-link>
                        <!-- <a href="#"  @click="openRelease = true" class="nav-link">Release</a> -->
                    </li>
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="navbarDropdown"
                            role="button"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >Reports</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <router-link
                                to="/released-documents"
                                class="dropdown-item"
                            >Released Documents</router-link>
                            <router-link
                                to="/received-documents"
                                class="dropdown-item"
                            >Received Documents</router-link>
                            <div class="dropdown-divider"></div>
                            <router-link
                                to="/unacted-documents"
                                class="dropdown-item"
                            >Unacted Documents</router-link>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="navbarDropdown"
                            role="button"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >Settings</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a href="register" class="dropdown-item" v-if="isAdmin">Register</a>
                            <router-link to="/change-password" class="dropdown-item">Change Password</router-link>
                            <router-link to="/add-notes" class="dropdown-item" v-if="isAdmin">Add Notes</router-link>
                            <router-link to="/generate-barcodes" class="dropdown-item" v-if="isAdmin">Genereate Barcodes</router-link>
                            <div class="dropdown-divider"></div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <button
                            class="nav-link btn btn-link"
                            data-toggle="modal"
                            data-target="#exampleModal"
                        >
                            Updates
                            <span v-if="!badge" class="badge badge-pill badge-danger">New</span>
                        </button>
                    </li>
                </ul>
                <span class="navbar-text">Welcome {{ user.name.name }}</span>&nbsp&nbsp
                <div class="form-inline my-2 my-lg-0">
                    <a href="#" v-on:click="logout()" class="btn btn-outline-danger my-2 my-sm-0">
                        <span class="fa fa-power-off"></span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Modal Update-->
        <div
            class="modal fade"
            id="exampleModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Changelog</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-for="item in updates">
                        <span v-html="item.updates"></span>
                        </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                            @click="seen()"
                        >Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal update -->
    </div>
</template> 

<script>
export default {
    data() {
        return {
            user: {
                name: "",
                office: ""
            },
            badge: false,
            updates: [],
            isAdmin: false
        };
    },

    mounted() {
        this.getUser();

        axios
            .get("seen-badge")
            .then(response => {
                if (response.data) {
                    this.badge = true;
                }
            })
            .catch(error => (this.errors = error.response.data.errors));

        axios
            .get("updates")
            .then(response => {
                this.updates = response.data;
            })
            .catch(error => (this.errors = error.response.data.errors));
    },

    methods: {
        logout() {
            axios
                .post("logout")
                .then(response => {
                    window.location.href = "";
                })
                .catch(error => (this.errors = error.response.data.errors));
        },

        getUser() {
            axios
                .post("get-user")
                .then(response => {
                    this.user.name = response.data;
                    this.$root.user.office_name =
                        response.data.office.office_name;
                    this.$root.user.user_name = response.data.name;
                    this.$root.user.user_id = response.data.id;
                    if (response.data.is_admin == 1) {
                       this.isAdmin = true
                    } else {
                        this.isAdmin = false
                    }
                })
                .catch(error => (this.errors = error.response.data.errors));
        },

        seen() {
            axios
                .post("seen")
                .then(response => {
                    this.badge = true;
                })
                .catch(error => (this.errors = error.response.data.errors));
        }
    }
};
</script>