import requests
from rasa_sdk import Action
from rasa_sdk.executor import CollectingDispatcher

class ActionGetIncidentList(Action):

    def name(self) -> str:
        return "action_get_incident_list"

    def run(self, dispatcher: CollectingDispatcher, tracker, domain):
        # URL du backend PHP qui retourne la liste des incidents
        url = "http://localhost/karenbot?action=get_incidents"

        try:
            # Faire une requête GET au backend PHP pour récupérer la liste des incidents
            response = requests.get(url)
            if response.status_code == 200:
                data = response.json()
                incidents_list = data.get('incidents', "Aucun incident trouvé.")
                # Répondre avec la liste des incidents
                dispatcher.utter_message(text=incidents_list)
            else:
                dispatcher.utter_message(text="Impossible de récupérer la liste des incidents.")
        
        except Exception as e:
            dispatcher.utter_message(text=f"Une erreur est survenue : {str(e)}")

        return []

class ActionGetAppelList(Action):

    def name(self) -> str:
        return "action_get_appel_list"

    def run(self, dispatcher: CollectingDispatcher, tracker, domain):
        # URL of the PHP backend for retrieving "Appels" data
        url = "http://localhost/karenbot?action=get_appels"

        try:
            # Send a GET request to the PHP backend
            response = requests.get(url)
            if response.status_code == 200:
                data = response.json()
                appels_list = data.get('appels', "Aucun appel trouvé.")
                
                # Respond with the list of "Appels"
                dispatcher.utter_message(text=appels_list)
            else:
                dispatcher.utter_message(text="Impossible de récupérer la liste des appels.")
        
        except Exception as e:
            dispatcher.utter_message(text=f"Une erreur est survenue : {str(e)}")

        return []
    
class ActionGetDemandeList(Action):

    def name(self) -> str:
        return "action_get_demande_list"

    def run(self, dispatcher: CollectingDispatcher, tracker, domain):
        # URL of the PHP backend for retrieving "Appels" data
        url = "http://localhost/karenbot?action=get_demandes"

        try:
            # Send a GET request to the PHP backend
            response = requests.get(url)
            if response.status_code == 200:
                data = response.json()
                demandes_list = data.get('demandes', "Aucune demande trouvé.")
                
                # Respond with the list of "Demandes"
                dispatcher.utter_message(text=demandes_list)
            else:
                dispatcher.utter_message(text="Impossible de récupérer la liste des demandes.")
        
        except Exception as e:
            dispatcher.utter_message(text=f"Une erreur est survenue : {str(e)}")

        return []