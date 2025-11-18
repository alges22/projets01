import {defineStore} from "pinia";
import {useApi} from "~/helpers/useApi";
const api = useApi();
export const useFileStatusStores = defineStore('fileStatus', {
    state: ()=> {
        return {
            demands: [],
            
        }
    },
    actions: {
        async setDemands(){
            try{
                
                const response = await api({
                    method: "get",
                    // url: 'client/get-vehicles',
                    url: 'client/demands',
                })
                if (response.status === 200) {
                    this.demands = response.data;
                    
                }
            }catch(error){
                console.log("une erreur est survenue")
                console.log(error);
            }
        }
    }
})