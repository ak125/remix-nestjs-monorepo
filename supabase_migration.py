import os
import supabase
from supabase import create_client
from dotenv import load_dotenv
import time
from pathlib import Path

load_dotenv()

class MigrationManager:
    def __init__(self):
        self.client = self.init_supabase()
        self.batch_size = 50  # Réduire la taille des batches

    def init_supabase(self):
        supabase_url = os.getenv("SUPABASE_URL")
        supabase_key = os.getenv("SUPABASE_KEY")
        return create_client(supabase_url, supabase_key)

    def execute_sql(self, sql):
        """Exécute une requête SQL via l'API REST"""
        try:
            response = self.client.postgrest.rpc('execute', {'sql': sql})
            return response
        except Exception as e:
            print(f"Erreur d'exécution: {str(e)}")
            return None

    def process_migration_file(self, file_path):
        with open(file_path, 'r', encoding='utf-8', errors='replace') as f:
            content = f.read()
        
        # Découpage intelligent des requêtes
        queries = []
        current_query = []
        in_string = False
        
        for char in content:
            if char == "'": in_string = not in_string
            if char == ';' and not in_string:
                queries.append(''.join(current_query).strip())
                current_query = []
            else:
                current_query.append(char)
        
        return queries

    def run_migration(self, migration_path):
        queries = self.process_migration_file(migration_path)
        
        for i, query in enumerate(queries):
            if not query: continue
            
            print(f"🔧 Requête {i+1}/{len(queries)}: {query[:80]}...")
            
            for attempt in range(3):
                try:
                    response = self.execute_sql(query)
                    if response and response.status_code // 100 != 2:
                        raise Exception(f"Erreur {response.status_code}: {response.text}")
                    break
                except Exception as e:
                    if attempt == 2:
                        print(f"❌ Échec après 3 tentatives: {str(e)}")
                        return False
                    print(f"🔄 Tentative {attempt+1}/3 échouée. Nouvel essai dans 5s...")
                    time.sleep(5)
        
        return True

if __name__ == "__main__":
    manager = MigrationManager()
    result = manager.run_migration("/workspaces/nestjs-remix-monorepo/migration_part_aa.fixed")
    
    if result:
        print("✅ Migration réussie")
    else:
        print("🔥 Échec de la migration")