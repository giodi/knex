import os.path as path
import subprocess

DATA_PATH = str(path.abspath(path.join(__file__ ,"../../data/input")))
BIN_PATH = str(path.abspath(path.join(__file__ ,"../../.pixi/envs/default/bin")))
SLING_CONFIG = str(path.abspath(path.join(__file__ ,"../../.sling")))

subprocess.run(f"export DUCKDB='duckdb:///{DATA_PATH}/deskriptoren.db'; {BIN_PATH}/sling run --src-stream file:///{DATA_PATH}/personendeskriptoren.csv --src-options '{{\"delimiter\": \";\"}}' --tgt-conn duckdb:///{DATA_PATH}/deskriptoren.db --tgt-object {{stream_file_folder}}.{{stream_file_name}} --mode full-refresh; {BIN_PATH}/sling run --src-stream file:///{DATA_PATH}/personendeskriptoren_zusatz.csv --src-options '{{\"header\": false}}' --tgt-conn duckdb:///{DATA_PATH}/deskriptoren.db --tgt-object {{stream_file_folder}}.{{stream_file_name}} --mode full-refresh", shell=True)