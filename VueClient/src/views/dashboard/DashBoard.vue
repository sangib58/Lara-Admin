<template>
    <v-container>
        <v-row v-if="roleId==1" no-gutters>
            <v-col class="pl-2" cols="12" sm="3">
                <v-card class="grey lighten-1">
                    <v-card-title>
                        <v-icon x-large left>account_circle</v-icon>       
                    </v-card-title>
                    <v-card-text>Total <span class="black--text">{{totalUser}}</span></v-card-text>
                </v-card>
            </v-col>
            <v-col class="pl-2" cols="12" sm="3">
                <v-card class="grey lighten-2">
                    <v-card-title>
                        <v-icon x-large left>verified_user</v-icon>       
                    </v-card-title>
                    <v-card-text>Active <span class="black--text">{{activeUser}}</span></v-card-text>
                </v-card>
            </v-col>
            <v-col class="pl-2" cols="12" sm="3">
                <v-card class="grey lighten-3">
                    <v-card-title>
                        <v-icon x-large left>thumb_down_off_alt</v-icon>       
                    </v-card-title>
                    <v-card-text>In-Active <span class="black--text">{{inActiveUser}}</span></v-card-text>
                </v-card>
            </v-col>
            <v-col class="pl-2" cols="12" sm="3">
                <v-card class="grey lighten-4">
                    <v-card-title>
                        <v-icon x-large left>admin_panel_settings</v-icon>       
                    </v-card-title>
                    <v-card-text>Admin <span class="black--text">{{adminUser}}</span></v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="6">
                <line-chart v-if="loadDate" :chartdata="dateLineChartdata" :options="chartOptions"/>
            </v-col>
            <v-col cols="6">
                <pie-chart v-if="loadRole" :chartdata="userPiedata" :options="chartOptions"/>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="6">
                <bar-chart v-if="loadMonth" :chartdata="monthBarChartdata" :options="BarChartOptions"/>
            </v-col>
            <v-col cols="6">
                <bar-chart v-if="loadBrowse" :chartdata="browserBarChartdata" :options="BarChartOptions"/>
            </v-col>
        </v-row>
    </v-container>  
</template>

<script>
import LineChart from '../../assets/chart/lineChart'
import BarChart from '../../assets/chart/barChart'
import PieChart from '../../assets/chart/pieChart'

export default {
    name:'Dashboard',
    components:{
        LineChart,
        BarChart,
        PieChart
    },
    data(){
        return{
            totalUser:'',
            activeUser:'',
            inActiveUser:'',
            adminUser:'',
            loadDate:false,
            loadRole:false,
            loadMonth:false,
            loadBrowse:false,
            userPiedata:null,
            dateLineChartdata: null,
            monthBarChartdata:null,
            browserBarChartdata:null,
            chartOptions:null,
            BarChartOptions:null,
            userId:null        
        }
    },
    computed:{
        roleId:function(){           
            return this.$store.getters['dashboard/userInfo'].user_role_id
        }
    },
    mounted(){
        this.loadDate=false
        this.loadRole=false
        this.loadMonth=false
        this.loadBrowse=false
        try {
            this.userId=parseInt(localStorage.getItem('loggedUserId'))

            this.$store.dispatch('dashboard/fetchRoleWiseUser',this.userId)
            .then((response)=>{
                if(response.status==200){          
                    this.userPiedata={                  
                        labels: response.data.return.map(x=>x.roleName),              
                        datasets: [
                            {
                                label: 'User Count',
                                backgroundColor: '#42424',
                                data: response.data.return.map(x=>x.count),                      
                            }
                        ]           
                    }
                    this.loadRole=true
                }
            })
            .catch((err)=>{
                console.log(err)
            })

            
            this.$store.dispatch('dashboard/fetchDateLoginCount',this.userId)
            .then((response)=>{
                if(response.status==200){ 
                    this.dateLineChartdata={               
                        labels: response.data.return.map(x=>x.date.substr(0,10)),           
                        datasets: [
                            {
                                label: 'Date wise Login',
                                backgroundColor: '#42424',
                                data: response.data.return.map(x=>x.count)                       
                            }
                        ]           
                    }  
                    this.loadDate=true               
                }
            })
            .catch((err)=>{
                console.log(err)
            })
            this.chartOptions= {
                responsive: true,
                maintainAspectRatio: false
            }

            this.$store.dispatch('dashboard/fetchMonthLoginCount',this.userId)
            .then((response)=>{
                if(response.status==200){ 
                    this.monthBarChartdata= {
                        labels: response.data.return.map(x=>x.month),
                        datasets: [
                            {
                                label: 'Month wise Login',
                                backgroundColor: '#42424',
                                data: response.data.return.map(x=>x.count)
                            }
                        ]
                    }     
                    this.loadMonth=true 
                }
            })
            .catch((err)=>{
                console.log(err)
            })

            this.$store.dispatch('dashboard/fetchBrowserLoginCount',this.userId)
            .then((response)=>{
                if(response.status==200){ 
                    this.browserBarChartdata= {
                        labels: response.data.return.map(x=>x.browser),
                        datasets: [
                            {
                                label: 'Browser wise Login',
                                backgroundColor: '#42424',
                                data: response.data.return.map(x=>x.count)
                            }
                        ]
                    }
                    this.loadBrowse=true              
                }
            })
            .catch((err)=>{
                console.log(err)
            })
            
            this.BarChartOptions= {
                scales:{
                    yAxes: [{
						ticks: {
							beginAtZero: true
						},
						gridLines: {
							display: true
						}
					}],
                    xAxes: [{
						ticks: {
							beginAtZero: true
						},
						gridLines: {
							display: false
						}
					}]
                },
                responsive: true,
                maintainAspectRatio: false
            }
        } catch (error) {
            console.log(error)
        }
    },
    created(){
        this.$store.dispatch('dashboard/fetchStatus')
        .then((response)=>{
            if(response.status==200){
                this.totalUser=response.data.return.totalUser
                this.activeUser=response.data.return.activeUser
                this.inActiveUser=response.data.return.inActiveUser
                this.adminUser=response.data.return.adminUser
            }
        })
        .catch((err)=>{
            console.log(err)
        })
    }
}
</script>

<style scoped>
    .small {
        max-width: 600px;
        margin:  150px auto;
    }
</style>