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
                try:
                    # First, try to parse as JSON
                    data = response.json()
                    incidents_list = data.get('incidents', "Aucun incident trouvé.")
                except ValueError:
                    # If it's not JSON, treat it as plain text (HTML in your case)
                    incidents_list = response.text

                # Répondre avec la liste des incidents
                dispatcher.utter_message(text=incidents_list)
            else:
                dispatcher.utter_message(text="Impossible de récupérer la liste des incidents.")
        
        except Exception as e:
            dispatcher.utter_message(text=f"Une erreur est survenue : {str(e)}")

        return []