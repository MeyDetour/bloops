import {Controller} from '@hotwired/stimulus';
import axios from "axios";
import Chart from 'chart.js/auto';
//get statistique of clients and reservations
//USE IN /home/mey/Documents/symfonyProject/reservation/templates/admin/statistiques/index.html.twig
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */


const moisDeLAnnee = [
    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];
const label = 'followers 🙋'
const today = new Date();
let currentYear = today.getFullYear();
let currentMonth = today.getMonth(); // Notez que getMonth() renvoie 0-11
let currentChart = null;
export default class extends Controller {
    static targets = ['chart', 'title', 'bouttons']
    static values = {userId:Number}

    connect() {
        this.statsOfOneMonth()
        /* const ctx = document.querySelector('#myChart');   =>   this */
    }

    updateChart(type, abscisse, data1) {
        if (currentChart) {
            currentChart.destroy();
        }

          let maxY = Math.max(...data1.data) + 6


        currentChart = new Chart(this.chartTarget, {
            type: type,
            data: {
                labels: abscisse,
                datasets: [{
                    label: data1.label,
                    backgroundColor: 'rgb(255, 99, 132, .4)',
                    borderColor: 'rgb(255, 99, 132)',
                    data: data1.data,
                    tension: 0.4
                },],
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        suggestedMax: maxY
                    }
                }
            }
        });
    }


    statsOfOneMonth() {
        this.titleTarget.textContent = ` Activité du mois de ${moisDeLAnnee[currentMonth]} ${currentYear}`
        const lastDayThisMonth = new Date(currentYear, currentMonth + 1, 0);
        const maxDayOfMonth = lastDayThisMonth.getDate();
        let jours = []
        for (let i = 1; i < maxDayOfMonth + 1; i++) {
            jours.push(i)
        }
        axios.post(`/stat/month/${currentMonth}/${currentYear}`,{'id':this.userIdValue}).then(response => {
            /*RETURN :  followers=[0, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0], */
            response = response.data

            this.updateChart('line', jours, {'label': label, 'data': response.request})
        }).catch((error) => {
            console.log('error',error)
        })


    }


    getLastMonthly() {
        console.log('click')
        if (currentMonth === 0) {
            currentMonth = 11; // Décembre
            currentYear--;
        } else {
            currentMonth--;
        }

        this.statsOfOneMonth();
    }

    getNextMonthly() {

        if (currentMonth === 11) { // Décembre
            currentMonth = 0; // Janvier
            currentYear++;
        } else {
            currentMonth++;
        }
        this.statsOfOneMonth();
    }

    comeTodayMonthly() {

        currentYear = today.getFullYear();
        currentMonth = today.getMonth();

        this.statsOfOneMonth();
    }

}
